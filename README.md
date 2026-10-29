# LatestiPhone — Deliverable 2

This project is an online shop prototype for a business that sells new and pre-owned iPhones in South Africa. I built the customer side and a separate admin area. Both parts use the same MySQL database.

## What the website can do

### Customer website
- Create an account and sign in.
- Browse iPhone models, colours, storage options and prices.
- Add products to the cart and continue to checkout.
- Choose delivery and view branch stock.
- Place an order or start a lay-by plan.
- View order details after signing in.
- Use the practice payment flow for PayFast, Ozow or Yoco.

### Admin website
- Sign in through `admin/login.php`.
- View the dashboard and manage products.
- Check and update branch stock.
- View customer orders and lay-by records.

The project uses PHP, MySQL, HTML, CSS and JavaScript. The database is accessed with PDO and prepared statements are used where user input is included in queries. The front-end layout uses Tailwind CSS from its CDN, along with Lucide icons.

## Project folders

- `index.php`, `shop.php`, `checkout.php`, `orders.php`, `layby.php` and the other root PHP files are used by customers.
- `admin/` contains the admin pages.
- `api/` contains endpoints used by the front end.
- `includes/` contains shared settings, database connection code, page layout and helper functions.
- `assets/css/` and `assets/js/` contain the stylesheets and JavaScript.
- `images/` contains the iPhone product images.
- `database/` contains the SQL files used to create and populate the database.
- `docs-diagrams/` contains the diagrams and page screenshots included with the project documentation.

## Setting it up locally

1. Install a PHP and MySQL environment such as MAMP.
2. Copy the `latest-iphone` folder into the local web server's document root.
3. Create a MySQL database and import `database/schema.sql` using phpMyAdmin.
4. Open `includes/config.php` and set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` and `DB_PASS` for your local database. These settings can also be supplied as environment variables.
5. Set `BASE_URL` to the folder path if the project is not in the web root. For example, use `/latest-iphone` when the URL contains that folder.
6. Start the web server and open the project URL in a browser.

For a hosted server, import `database/schema_hosting.sql` into the database created in the hosting control panel. Update the database settings and `BASE_URL` before uploading the project files. Keep database passwords out of submitted source code.

## Payment testing

The default setting is `GATEWAY_MODE=simulate`. This is for demonstration and testing only; it does not charge real money. The payment screens show a practice version of the available providers. Real payments require the correct merchant accounts, keys and provider configuration.

## Before submission

- Confirm that the database has been imported.
- Test registration and login.
- Test browsing, cart, checkout and order confirmation.
- Test the admin login and the product, stock, order and lay-by pages.
- Take screenshots from the hosted website and include them with the documentation.
- Use your own hosting database settings; do not submit private passwords.
