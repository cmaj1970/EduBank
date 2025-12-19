# Contributing to EduBank

Thank you for your interest in EduBank! Every contribution helps make this educational software better.

## How can I contribute?

### Report Bugs

1. Check if the bug has already been reported as an [Issue](https://github.com/cmaj1970/EduBank/issues)
2. Create a new issue using the Bug Report template
3. Describe exactly what happens and what you expected

### Suggest Features

1. Open an issue using the Feature Request template
2. Describe the feature and why it would be useful
3. Discuss with the community

### Contribute Code

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add feature'`)
4. Push the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

## Development Environment

### Requirements

- PHP 7.2+
- MySQL 5.7+
- Composer
- Optional: Docker/ddev

### Setup

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
composer install
cp config/app.default.php config/app.php
# Enter database credentials in config/app.php
bin/cake migrations migrate
bin/cake server
```

## Code Style

- PHP: PSR-12
- Indentation: Tabs
- Comments: English
- Commit messages: English, short and concise

## Questions?

- [GitHub Discussions](https://github.com/cmaj1970/EduBank/discussions) for general questions
- [GitHub Issues](https://github.com/cmaj1970/EduBank/issues) for bugs and features
