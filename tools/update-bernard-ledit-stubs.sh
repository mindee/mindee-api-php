#!/usr/bin/env bash
# Downloads the PHPStan stubs of the bernard-ledit version composer.json pins to
# stubs/bernard_ledit.stubs.php. Usage: update-bernard-ledit-stubs.sh [tag], e.g. v0.2.0 for another
# release, or "latest" for the latest one (pre-releases excluded).
set -euo pipefail

readonly REPO="mindee/bernard-ledit"
repo_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
readonly repo_root
readonly OUT="$repo_root/stubs/bernard_ledit.stubs.php"

die() {
  echo "error: $*" >&2
  exit 1
}

# Prints the tag of the version composer.json pins, from its "conflict": {"ext-bernard_ledit": "<X.Y.Z"}.
pinned_tag() {
  local constraint
  # shellcheck disable=SC2016 # $argv is PHP's, not the shell's
  constraint="$(php -r 'echo json_decode(file_get_contents($argv[1]), true)["conflict"]["ext-bernard_ledit"] ?? "";' \
    "$repo_root/composer.json")"
  [[ "$constraint" =~ ^<[[:space:]]*([0-9]+\.[0-9]+\.[0-9]+[^[:space:]]*)$ ]] ||
    die "composer.json should pin the extension as \"conflict\": {\"ext-bernard_ledit\": \"<X.Y.Z\"}; found '$constraint'"
  printf 'v%s\n' "${BASH_REMATCH[1]}"
}

# GitHub redirects /releases/latest to the newest release that is neither a draft nor a pre-release.
latest_tag() {
  local url
  url="$(curl -fsSLI -o /dev/null -w '%{url_effective}' "https://github.com/$REPO/releases/latest")" ||
    die "could not reach GitHub"
  [[ "$url" == */releases/tag/* ]] || die "$REPO has no release yet (pre-releases don't count)"
  printf '%s\n' "${url##*/}"
}

main() {
  local tag="${1:-}"
  if [ -z "$tag" ]; then
    tag="$(pinned_tag)" || exit 1
  elif [ "$tag" = latest ]; then
    tag="$(latest_tag)" || exit 1
  fi
  mkdir -p "$(dirname "$OUT")"
  curl -fsSL -o "$OUT.tmp" "https://raw.githubusercontent.com/$REPO/$tag/bindings/php/bernard_ledit.stubs.php" ||
    { rm -f "$OUT.tmp"; die "no stubs found for $tag"; }
  # Records the version right after the opening tag, so reviewers can see which release the stubs match.
  if [ "$(head -n 1 "$OUT.tmp")" != "<?php" ]; then
    rm -f "$OUT.tmp"
    die "the stubs for $tag don't start with <?php"
  fi
  {
    echo "<?php"
    echo "// Generated from $REPO $tag by tools/update-bernard-ledit-stubs.sh: don't edit by hand."
    tail -n +2 "$OUT.tmp"
  } > "$OUT"
  rm -f "$OUT.tmp"
  echo "Updated $OUT to bernard-ledit $tag"
}

main "$@"
