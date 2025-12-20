# EduBank

**Open-Source Banking Simulation for Schools**

EduBank is educational software that teaches students at schools how to use online banking. The software simulates real banking processes in a safe learning environment.

## Features

- **Virtual Accounts** – Each student gets their own account with starting balance
- **Transfers** – Transfer money between accounts
- **TAN Authentication** – Confirm transactions with TAN codes
- **Multi-Tenant** – Multiple schools on one instance
- **Responsive Design** – Works on desktop and mobile devices

## Who is EduBank for?

- **Teachers** – Teaching material for financial literacy
- **Students** – Hands-on practice without real money
- **Schools** – Free, easy-to-install solution

## Installation

### Requirements

- PHP 7.2 or higher
- MySQL 5.7 or higher
- Composer

### With Docker (recommended)

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
cp config/app.default.php config/app.php
docker-compose build
docker-compose up -d
```

Initialize database (after start):
```bash
docker-compose exec -T db mysql -u root -proot edubank < db/schema.sql
docker-compose exec -T db mysql -u root -proot edubank < db/seed.sql
```

Then: http://localhost:8080

### Manual

See [INSTALL.en.md](INSTALL.en.md) for full instructions.

## Technology

- **Backend:** CakePHP 3.6
- **Frontend:** Custom CSS (responsive)
- **Database:** MySQL

## License

MIT License – see [LICENSE](LICENSE)

## Support the Project

EduBank is a volunteer project. Development and operation cost time and money.

If EduBank helps your school, I'd appreciate a voluntary contribution:

**[Support via PayPal](https://www.paypal.com/paypalme/carlmajneri)**

Thank you!

## Contributing

Contributions are welcome! See [CONTRIBUTING.md](CONTRIBUTING.en.md) for details.

## Support

- **Issues:** [GitHub Issues](https://github.com/cmaj1970/EduBank/issues)
- **Discussion:** [GitHub Discussions](https://github.com/cmaj1970/EduBank/discussions)
