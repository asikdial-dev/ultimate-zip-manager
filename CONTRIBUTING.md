
---

## File 3: `CONTRIBUTING.md`

```markdown
# Contributing to Ultimate ZIP Manager

First off, thank you for considering contributing to Ultimate ZIP Manager! 🎉

## How Can I Contribute?

### 🐛 Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- **Description** — Clear description of the bug
- **Steps to Reproduce** — Step-by-step instructions
- **Expected Behavior** — What should happen
- **Actual Behavior** — What actually happens
- **Environment** — PHP version, hosting platform, browser
- **Screenshots** — If applicable

### 💡 Suggesting Features

Feature requests are welcome! Please provide:

- **Use Case** — Why is this feature needed?
- **Proposed Solution** — How should it work?
- **Alternatives** — Any alternative solutions considered?

### 🔧 Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

#### Pull Request Guidelines

- Follow PSR-12 coding standards
- Add comments for complex logic
- Test on multiple PHP versions (7.4, 8.0, 8.1)
- Update documentation if needed
- One feature per PR

### 📝 Code Style

```php
<?php
// Use meaningful variable names
$uploadedFiles = [];  // ✅ Good
$uf = [];             // ❌ Bad

// Add comments for complex logic
// Calculate chunk size based on file size and connection speed
$optimalChunkSize = min($fileSize / 100, 2 * 1024 * 1024);

// Use type hints
function processChunk(string $filename, int $chunkIndex): bool {
    // ...
}
