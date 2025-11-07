# Release Checklist for TogoMQ SDK PHP

Use this checklist before publishing a new release.

## Pre-Release Checklist

### Code Quality
- [ ] All unit tests pass (`composer test`)
- [ ] Code style is correct (`composer cs-check` or run `composer cs-fix`)
- [ ] Static analysis passes (`composer analyse`)
- [ ] No security vulnerabilities (`composer audit`)
- [ ] Code coverage is maintained or improved

### Documentation
- [ ] README.md is up to date
- [ ] CHANGELOG.md has entry for new version
- [ ] All public methods have PHPDoc comments
- [ ] Examples are working and up to date
- [ ] AGENTS.md reflects any architectural changes

### Testing
- [ ] All unit tests pass on PHP 8.1, 8.2, 8.3
- [ ] Examples run successfully
- [ ] Integration testing with real TogoMQ server (if applicable)
- [ ] Error scenarios are tested

### Version Management
- [ ] Version number follows Semantic Versioning
- [ ] composer.json version is updated (if applicable)
- [ ] CHANGELOG.md has correct version and date
- [ ] Breaking changes are documented

## Release Process

### 1. Update Version Information

```bash
# Update CHANGELOG.md
# Change [Unreleased] to [X.Y.Z] - YYYY-MM-DD
# Add new [Unreleased] section at the top

# Example:
# ## [Unreleased]
# 
# ## [1.0.0] - 2024-11-07
# ### Added
# - Feature X
```

### 2. Commit Version Changes

```bash
git add CHANGELOG.md
git commit -m "Prepare release vX.Y.Z"
git push origin main
```

### 3. Create Git Tag

```bash
# Create annotated tag
git tag -a vX.Y.Z -m "Release vX.Y.Z"

# Push tag to trigger release workflow
git push origin vX.Y.Z
```

### 4. Verify GitHub Actions

- [ ] CI workflow passes
- [ ] Release workflow creates GitHub release
- [ ] Release notes are generated correctly

### 5. Publish to Packagist (First Time Only)

- [ ] Create account on packagist.org
- [ ] Submit package: https://packagist.org/packages/submit
- [ ] Add GitHub webhook for auto-updates
- [ ] Verify package appears on Packagist

### 6. Post-Release

- [ ] GitHub release is created
- [ ] Release notes are accurate
- [ ] Packagist shows new version
- [ ] Installation works: `composer require togomq/togomq-sdk:^X.Y.Z`
- [ ] Documentation links work

## Version Numbering Guidelines

### MAJOR version (X.0.0)
- Breaking API changes
- Incompatible changes to public interfaces
- Removal of deprecated features

### MINOR version (0.X.0)
- New features (backwards compatible)
- New public methods/classes
- Deprecations (with backwards compatibility)

### PATCH version (0.0.X)
- Bug fixes
- Documentation updates
- Internal refactoring
- Performance improvements

## Post-Release Communication

- [ ] Update repository description
- [ ] Share on relevant channels (if applicable)
- [ ] Monitor issues for bug reports
- [ ] Respond to community feedback

## Rollback Procedure

If critical issues are found after release:

1. Delete the git tag:
   ```bash
   git tag -d vX.Y.Z
   git push origin :refs/tags/vX.Y.Z
   ```

2. Delete GitHub release (via GitHub UI)

3. Fix issues and create new patch version

## Notes

- Always test on a clean installation before releasing
- Keep a local backup before deleting tags
- Monitor Packagist after first few releases
- Consider creating release candidates (vX.Y.Z-rc1) for major versions

## First Release Special Steps

For the initial v0.1.0 release:

- [ ] Ensure all core functionality works
- [ ] Complete all documentation
- [ ] Set up Packagist account and package
- [ ] Configure GitHub repository settings
- [ ] Add repository topics/tags
- [ ] Set up GitHub webhooks for Packagist
- [ ] Create comprehensive release notes
- [ ] Test installation from Packagist

## Emergency Hotfix Process

For critical security or bug fixes:

1. Create hotfix branch from tag
2. Fix issue
3. Test thoroughly
4. Bump patch version
5. Tag and release
6. Merge back to main

```bash
git checkout -b hotfix/vX.Y.Z+1 vX.Y.Z
# ... make fixes ...
git commit -m "Fix critical issue"
git tag -a vX.Y.Z+1 -m "Hotfix release"
git push origin vX.Y.Z+1
git checkout main
git merge hotfix/vX.Y.Z+1
```
