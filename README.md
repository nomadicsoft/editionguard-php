# Powered by

[![N|Solid](https://i1.wp.com/nomadicsoft.io/wp-content/uploads/2019/11/logo.png?w=467&ssl=1)](https://nomadicsoft.io/)

This package implements [Edition Guard](https://editionguard.com)\'s REST api.

### Installation
```sh
$ composer require nomadicsoft/editionguard-php
```

### Basic usage
```sh
# Create an api handler instance.
$editionGuard = new \NomadicSoft\EditionGuard\EditionGuard('API_TOKEN');
# Execute command.
$editionGuard->getBooks();
```

### Laravel support (Optional)
```sh
You can do this things if you want to access an api via Laravels Facade class.

# Publish configs.
$ php artisan vendor:publish --provider=NomadicSoft\Laravel\Providers\EditionGuardServiceProvider
# Add to .env
EDITION_GUARD_API_TOKEN="YOUR_TOKEN"

Now you can use the Facade class "NomadicSoft\Laravel\Facades\EditionGuard" to access Edition Guard api.

# For example
NomadicSoft\Laravel\Facades\EditionGuard::getBooks();
```

### Supported methods

```sh
 * @method getBook(int $id)
 * @method getBooks(array $optional = [])
 * @method storeBook(string $title, string $resource, array $optional = [])
 * @method updateBook(int $id, string $title, string $resource, array $optional = [])
 * @method deleteBook(int $id)
 * @method generateBookLinks(int $id, int $count)
 * @method deliverBookLink(int $resourceId, string $email, array $optional = [])
 * @method deliverBookLinks(array $bookList, string $email, array $optional = [])
 * @method download(array $optional = [])
 * @method getTransaction(int $id)
 * @method getTransactions(array $optional = [])
 * @method createTransaction(int $resourceId, array $optional = [])
 * @method updateTransaction(string $id, string $resourceId, bool $showInstructions, string $watermarkName, string $watermarkEmail, string $watermarkPhone, bool $watermarkPlaceBegin, bool $watermarkPlaceEnd, bool $watermarkPlaceRandom, string $watermarkPlaceRandomCount, string $usesRemaining, array $optional = [])
 * @method deleteTransaction(string $id)
 * @method getMasterLink(string $id)
 * @method getMasterLinks()
 * @method createMasterLink(int $resourceId, array $optional = [])
 * @method updateMasterLink(string $id, string $resourceId, array $optional = [])
 * @method deleteMasterLink(string $id)
```