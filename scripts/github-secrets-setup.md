# GitHub Secrets Configuration Guide

## Required Secrets for CI/CD Pipeline

### 1. Navigate to GitHub Repository Settings
Go to: `https://github.com/YOUR_USERNAME/YOUR_REPO/settings/secrets/actions`

### 2. Add the Following Secrets

#### Staging Environment Secrets

**STAGING_HOST**
- Description: Staging server hostname or IP address
- Example: `staging.yourdomain.com` or `192.168.1.100`
- How to get: From your hosting provider

**STAGING_USER**
- Description: SSH username for staging server
- Example: `deploy` or `ubuntu`
- How to get: Created during server setup

**STAGING_SSH_KEY**
- Description: Private SSH key for staging server authentication
- How to generate:
  ```bash
  ssh-keygen -t ed25519 -C "github-actions-staging" -f ~/.ssh/github_staging
  # Copy the PRIVATE key content
  cat ~/.ssh/github_staging
  # Add the PUBLIC key to server's authorized_keys
  cat ~/.ssh/github_staging.pub >> ~/.ssh/authorized_keys
  ```
- Security: Never commit this key, only add to GitHub Secrets

**STAGING_URL**
- Description: Full URL of staging environment
- Example: `https://staging.yourdomain.com`

---

#### Production Environment Secrets

**PRODUCTION_HOST**
- Description: Production server hostname or IP address
- Example: `yourdomain.com` or `192.168.1.200`

**PRODUCTION_USER**
- Description: SSH username for production server
- Example: `deploy` or `ubuntu`

**PRODUCTION_SSH_KEY**
- Description: Private SSH key for production server
- How to generate:
  ```bash
  ssh-keygen -t ed25519 -C "github-actions-production" -f ~/.ssh/github_production
  cat ~/.ssh/github_production
  ```

**PRODUCTION_URL**
- Description: Full URL of production environment
- Example: `https://yourdomain.com`

---

#### Notification Secrets

**SLACK_WEBHOOK**
- Description: Slack webhook URL for deployment notifications
- How to get:
  1. Go to https://api.slack.com/apps
  2. Create new app or select existing
  3. Enable "Incoming Webhooks"
  4. Create webhook for your channel
  5. Copy webhook URL
- Example: `https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXX`

---

### 3. Additional Optional Secrets

**DOCKER_REGISTRY_TOKEN** (if using private registry)
- For pushing Docker images to private registry

**CODECOV_TOKEN** (if using Codecov)
- For uploading test coverage reports

**SENTRY_DSN** (if using Sentry)
- For error tracking and monitoring

---

## Verification

After adding secrets, they should appear in:
`Settings > Secrets and variables > Actions > Repository secrets`

You should see:
- ✅ STAGING_HOST
- ✅ STAGING_USER
- ✅ STAGING_SSH_KEY
- ✅ STAGING_URL
- ✅ PRODUCTION_HOST
- ✅ PRODUCTION_USER
- ✅ PRODUCTION_SSH_KEY
- ✅ PRODUCTION_URL
- ✅ SLACK_WEBHOOK

---

## Testing the Setup

1. Push a commit to `staging` branch to trigger staging deployment
2. Check the Actions tab for workflow execution
3. Verify deployment via Slack notification
4. Access staging URL to confirm deployment

---

## Security Best Practices

✅ **DO:**
- Rotate SSH keys regularly
- Use separate keys for staging and production
- Limit SSH key permissions (read-only where possible)
- Enable 2FA on GitHub account
- Audit secret access logs regularly

❌ **DON'T:**
- Share SSH private keys
- Commit secrets to repository
- Use same credentials across environments
- Store secrets in code or comments
