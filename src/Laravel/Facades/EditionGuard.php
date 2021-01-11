<?php

namespace NomadicSoft\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getBook(int $id)
 * @method static getBooks(array $optional = [])
 * @method static storeBook(string $title, string $resource, array $optional = [])
 * @method static updateBook(int $id, string $title, string $resource, array $optional = [])
 * @method static deleteBook(int $id)
 * @method static generateBookLinks(int $id, int $count)
 * @method static deliverBookLink(int $resourceId, string $email, array $optional = [])
 * @method static deliverBookLinks(array $bookList, string $email, array $optional = [])
 * @method static download(array $optional = [])
 * @method static getTransaction(int $id)
 * @method static getTransactions(array $optional = [])
 * @method static createTransaction(int $resourceId, array $optional = [])
 * @method static updateTransaction(string $id, string $resourceId, bool $showInstructions, string $watermarkName, string $watermarkEmail, string $watermarkPhone, bool $watermarkPlaceBegin, bool $watermarkPlaceEnd, bool $watermarkPlaceRandom, string $watermarkPlaceRandomCount, string $usesRemaining, array $optional = [])
 * @method static deleteTransaction(string $id)
 * @method static getMasterLink(string $id)
 * @method static getMasterLinks()
 * @method static createMasterLink(int $resourceId, array $optional = [])
 * @method static updateMasterLink(string $id, string $resourceId, array $optional = [])
 * @method static deleteMasterLink(string $id)
 */
class EditionGuard extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \NomadicSoft\EditionGuard\EditionGuard::class;
    }
}
