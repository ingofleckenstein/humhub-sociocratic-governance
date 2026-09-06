#!/usr/bin/env bash
# SPDX-License-Identifier: AGPL-3.0-only
# Manual deployment: root downloads/copies; website user runs HumHub commands.
set -Eeuo pipefail
umask 022

die() { printf 'ERROR: %s\n' "$*" >&2; exit 1; }
trap 'printf "Deployment failed at line %s. Check files and database before retrying; no automatic rollback.\n" "$LINENO" >&2' ERR

MODULE_ID=sociocratic-governance
: "${DEPLOY_USER:?Set DEPLOY_USER to the website system user}"
[[ "$EUID" -eq 0 ]] || die "Run this script as root (including dry-run)."
: "${DOWNLOAD_ROOT:?Set DOWNLOAD_ROOT to an existing root-private download directory}"
REPOSITORY=https://github.com/ingofleckenstein/humhub-sociocratic-governance.git
: "${HUMHUB_ROOT:?Set HUMHUB_ROOT to the installation directory containing protected/yii}"
SITE_ROOT="$HUMHUB_ROOT"
PHP_BIN="${PHP_BIN:-php}"
BRANCH="${DEPLOY_BRANCH:-main}"
MODE="${1:---dry-run}"
[[ "$MODE" == --apply || "$MODE" == --dry-run ]] || die "Usage: bash deploy-test.sh [--dry-run|--apply]"

for tool in git rsync realpath flock find runuser id chown chmod stat; do command -v "$tool" >/dev/null || die "Missing: $tool"; done
PHP_BIN="$(command -v "$PHP_BIN")" || die "PHP_BIN is not executable."
[[ "$(id -u "$DEPLOY_USER")" -ne 0 ]] || die "DEPLOY_USER must not be root."
DEPLOY_GROUP="$(id -gn "$DEPLOY_USER")" || die "Website user does not exist."
runuser -u "$DEPLOY_USER" -- "$PHP_BIN" -v >/dev/null
git check-ref-format --branch "$BRANCH" >/dev/null

SITE_ROOT="$(realpath -e -- "$SITE_ROOT")"
[[ "$SITE_ROOT" != / ]] || die "Invalid installation root."
[[ -f "$SITE_ROOT/protected/yii" && -d "$SITE_ROOT/protected/config" ]] || die "Set HUMHUB_ROOT to the directory containing protected/yii."
MODULES="$SITE_ROOT/protected/modules"
[[ -d "$MODULES" && ! -L "$MODULES" ]] || die "Expected non-symlink protected/modules."
[[ "$(realpath -e -- "$MODULES")" == "$SITE_ROOT/protected/modules" ]] || die "Modules path escapes the test site."
[[ -w "$MODULES" ]] || die "No write access to modules."
TARGET="$MODULES/$MODULE_ID"
[[ ! -L "$TARGET" ]] || die "Module destination must not be a symlink."
[[ ! -e "$TARGET" || -d "$TARGET" ]] || die "Destination is not a directory."

[[ "$(realpath -m -- "$TARGET")" == "$SITE_ROOT/protected/modules/$MODULE_ID" ]] || die "Unsafe destination."

# Explicit root-private checkout location; never infer a production site.
[[ "$DOWNLOAD_ROOT" == /* && "$HUMHUB_ROOT" == /* ]] || die "Use absolute paths."
[[ -d "$DOWNLOAD_ROOT" && ! -L "$DOWNLOAD_ROOT" ]] || die "Create a root-private download directory first."
[[ "$(realpath -e -- "$DOWNLOAD_ROOT")" == "$(realpath -ms -- "$DOWNLOAD_ROOT")" ]] || die "Download path contains symlinks."
DOWNLOAD_ROOT="$(realpath -e -- "$DOWNLOAD_ROOT")"
[[ "$DOWNLOAD_ROOT" != / ]] || die "Invalid download root."
[[ "$(stat -c %u "$DOWNLOAD_ROOT")" == 0 && "$(stat -c %a "$DOWNLOAD_ROOT")" == 700 ]] || die "Download directory must be owned by root with mode 700."
[[ "$DOWNLOAD_ROOT/" != "$SITE_ROOT/"* && "$SITE_ROOT/" != "$DOWNLOAD_ROOT/"* ]] || die "Download and installation directories must not overlap."
STAGING="$DOWNLOAD_ROOT/$MODULE_ID"
[[ ! -L "$STAGING" ]] || die "Checkout must not be a symlink."
[[ ! -L "$DOWNLOAD_ROOT/.$MODULE_ID-deploy.lock" ]] || die "Lock must not be a symlink."
exec 9>"$DOWNLOAD_ROOT/.$MODULE_ID-deploy.lock"
flock -n 9 || die "Another deployment is running."
if [[ -d "$STAGING" ]]; then
    [[ -z "$(find "$STAGING" ! -user root -print -quit)" ]] || die "Checkout must be owned by root."
fi
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
runuser -u "$DEPLOY_USER" -- "$PHP_BIN" -r '$m=json_decode(stream_get_contents(STDIN),true,512,JSON_THROW_ON_ERROR); if (($m["id"]??null)!=="sociocratic-governance") exit(1);' < "$STAGING/module.json"
while IFS= read -r -d '' file; do runuser -u "$DEPLOY_USER" -- "$PHP_BIN" -l < "$file" >/dev/null; done < <(find "$STAGING" -path "$STAGING/.git" -prune -o -name '*.php' -type f -print0)

printf 'Commit: %s\nTarget: %s\n' "$(git -C "$STAGING" rev-parse HEAD)" "$TARGET"
OPTIONS=(-rlpt --chmod=D755,F644 --delete --itemize-changes --exclude=/.git/ --exclude=/.github/ --exclude=/scripts/ --exclude=/tests/ --exclude=/docs/ --exclude=/.gitignore)
if [[ "$MODE" == --dry-run ]]; then
    rsync "${OPTIONS[@]}" --dry-run "$STAGING/" "$TARGET/"
    printf 'Preview only. Root checkout updated; site, ownership and database unchanged. Use --apply to deploy.\n'
    exit 0
fi
printf 'Applying module files. Then ALL pending core and enabled-module migrations will run on this test installation.\n'
# Copy as root, then hand over ONLY this module directory.
rsync "${OPTIONS[@]}" "$STAGING/" "$TARGET/"
chown -hR -- "$DEPLOY_USER:$DEPLOY_GROUP" "$TARGET"
[[ -z "$(find "$TARGET" ! -user "$DEPLOY_USER" -print -quit)" ]] || die "Ownership handover failed."
cd "$SITE_ROOT/protected"
runuser -u "$DEPLOY_USER" -- "$PHP_BIN" yii cache/flush-all --interactive=0
runuser -u "$DEPLOY_USER" -- "$PHP_BIN" yii migrate/up --includeModuleMigrations=1 --interactive=0
runuser -u "$DEPLOY_USER" -- "$PHP_BIN" yii cache/flush-all --interactive=0
printf 'Deployment completed. On first install, activate the module in HumHub administration.\n'
