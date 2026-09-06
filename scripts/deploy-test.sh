#!/usr/bin/env bash
# SPDX-License-Identifier: AGPL-3.0-only
# Manual deployment only. Run as the website's system user, never root.
set -Eeuo pipefail
umask 022

die() { printf 'ERROR: %s\n' "$*" >&2; exit 1; }
trap 'printf "Deployment failed at line %s. Check files and database before retrying; no automatic rollback.\n" "$LINENO" >&2' ERR

MODULE_ID=sociocratic-governance
REPOSITORY=https://github.com/ingofleckenstein/humhub-sociocratic-governance.git
SITE_ROOT="${HUMHUB_ROOT:-/var/www/vhosts/sexpositiv.events/testcommunity.selbstsein.events}"
PHP_BIN="${PHP_BIN:-php}"
BRANCH="${DEPLOY_BRANCH:-main}"
MODE="${1:---dry-run}"
[[ "$MODE" == --apply || "$MODE" == --dry-run ]] || die "Usage: bash deploy-test.sh [--dry-run|--apply]"
[[ "$EUID" -ne 0 ]] || die "Run as the website system user, not root."
for tool in git rsync realpath flock find; do command -v "$tool" >/dev/null || die "Missing: $tool"; done
command -v "$PHP_BIN" >/dev/null || die "PHP_BIN is not executable."
git check-ref-format --branch "$BRANCH" >/dev/null

SITE_ROOT="$(realpath -e -- "$SITE_ROOT")"
[[ "${SITE_ROOT##*/}" == testcommunity.selbstsein.events ]] || die "Only the named test site is allowed."
[[ -f "$SITE_ROOT/protected/yii" && -d "$SITE_ROOT/protected/config" ]] || die "Set HUMHUB_ROOT to the directory containing protected/yii."
MODULES="$SITE_ROOT/protected/modules"
[[ -d "$MODULES" && ! -L "$MODULES" ]] || die "Expected non-symlink protected/modules."
[[ "$(realpath -e -- "$MODULES")" == "$SITE_ROOT/protected/modules" ]] || die "Modules path escapes the test site."
[[ -w "$MODULES" ]] || die "No write access to modules."
TARGET="$MODULES/$MODULE_ID"
[[ ! -L "$TARGET" ]] || die "Module destination must not be a symlink."
[[ ! -e "$TARGET" || -d "$TARGET" ]] || die "Destination is not a directory."
[[ "$(realpath -m -- "$TARGET")" == "$SITE_ROOT/protected/modules/$MODULE_ID" ]] || die "Unsafe destination."

[[ ! -L "$HOME/temp" ]] || die "~/temp must not be a symlink."
mkdir -p -- "$HOME/temp"
STAGING="$(realpath -e -- "$HOME/temp")/$MODULE_ID"
exec 9>"$HOME/temp/.$MODULE_ID-deploy.lock"
flock -n 9 || die "Another deployment is running."
[[ ! -L "$STAGING" ]] || die "Checkout must not be a symlink."
if [[ ! -e "$STAGING" ]]; then
    git clone --branch "$BRANCH" --single-branch "$REPOSITORY" "$STAGING"
else
    [[ -d "$STAGING/.git" ]] || die "Staging exists but is not a checkout."
    [[ "$(git -C "$STAGING" remote get-url origin)" == "$REPOSITORY" ]] || die "Unexpected origin."
    [[ -z "$(git -C "$STAGING" status --porcelain)" ]] || die "Staging contains local changes."
    [[ "$(git -C "$STAGING" branch --show-current)" == "$BRANCH" ]] || die "Unexpected checkout branch."
    git -C "$STAGING" fetch origin "$BRANCH"
    git -C "$STAGING" merge --ff-only "origin/$BRANCH"
fi
[[ "$(git -C "$STAGING" rev-parse HEAD)" == "$(git -C "$STAGING" rev-parse "origin/$BRANCH")" ]] || die "Checkout differs from remote."
[[ -z "$(find "$STAGING" -path "$STAGING/.git" -prune -o -type l -print -quit)" ]] || die "Source contains symlinks."
[[ ! -d "$TARGET" || -z "$(find "$TARGET" -type l -print -quit)" ]] || die "Destination contains symlinks."
[[ -f "$STAGING/Module.php" && -f "$STAGING/config.php" ]] || die "Missing module files."
"$PHP_BIN" -r '$m=json_decode(file_get_contents($argv[1]),true,512,JSON_THROW_ON_ERROR); if (($m["id"]??null)!=="sociocratic-governance") exit(1);' "$STAGING/module.json"
while IFS= read -r -d '' file; do "$PHP_BIN" -l "$file" >/dev/null; done < <(find "$STAGING" -path "$STAGING/.git" -prune -o -name '*.php' -type f -print0)

printf 'Commit: %s\nTarget: %s\n' "$(git -C "$STAGING" rev-parse HEAD)" "$TARGET"
OPTIONS=(-rlt --delete --itemize-changes --exclude=/.git/ --exclude=/.github/ --exclude=/scripts/ --exclude=/tests/ --exclude=/docs/ --exclude=/.gitignore)
if [[ "$MODE" == --dry-run ]]; then
    rsync "${OPTIONS[@]}" --dry-run "$STAGING/" "$TARGET/"
    printf 'Preview only. Checkout updated; site and database unchanged. Use --apply to deploy.\n'
    exit 0
fi
printf 'Applying module files. Then ALL pending core and enabled-module migrations will run on this test installation.\n'
rsync "${OPTIONS[@]}" "$STAGING/" "$TARGET/"
cd "$SITE_ROOT/protected"
"$PHP_BIN" yii cache/flush-all --interactive=0
"$PHP_BIN" yii migrate/up --includeModuleMigrations=1 --interactive=0
"$PHP_BIN" yii cache/flush-all --interactive=0
printf 'Deployment completed. On first install, activate the module in HumHub administration.\n'
