<?php

namespace NomadicSoft\EditionGuard;

use finfo;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EditionGuard
{
    /**
     * Edition Guard REST api token.
     *
     * @var string $apiToken
     */
    private $apiToken = '';

    /**
     * Edition Guard REST api main url.
     *
     * @var string $apiUrl
     */
    private $apiUrl = 'https://app.editionguard.com/api/v2/';

    /**
     * Default DRM
     */
    private $drm = self::DRM_ADOBE_HARDENED;

    /**
     * DRM Type id
     *
     * @var int
     */
    const DRM_ADOBE_LEGACY = 1;

    /**
     * DRM Type id
     *
     * @var int
     */
    const DRM_ADOBE_HARDENED = 2;

    /**
     * DRM Type id
     *
     * @var int
     */
    const DRM_EDITION_MARK = 3;

    /**
     * DRM Type id
     *
     * @var int
     */
    const DRM_EDITION_LINK = 4;

    /**
     * Initialise parameters.
     * @param string $apiToken
     */
    public function __construct(string $apiToken)
    {
        $this->setApiToken($apiToken);
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param string $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Returns all the attributes for the specified title.
     *
     * @param int $id
     * @return mixed
     * @throws GuzzleException
     */
    public function getBook(int $id)
    {
        $response = $this
            ->httpClient()
            ->get("book/{$id}")
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Return the list of books including the type of DRM, page count and other attributes.
     *
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function getBooks(array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->get('book', ['query' => $optional])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Upload a new book into EditionGuard.
     * Upon uploading a book, a number of DRM specific options are available.
     * Depending on your use case you can make books expire, such as the case for textbook rentals.
     * Or maybe your book should only be available on a single user's device.
     *
     * @param string $title
     * @param string $resource
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function saveBook(string $title, string $resource, array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->post('book', array_merge([
                'multipart' => [
                    [
                        'name'     => 'resource',
                        'contents' => $resource,
                        'filename' => 'book.' . $this->getResourceExtension($resource),
                    ],
                ],
                'query' => [
                    'title' => $title,
                    'drm' => $this->drm,
                ]
            ], $optional))
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Updates the specified eBook by setting the values of the parameters passed.
     * Any parameters not provided will be left unchanged.
     * Changes made to the DRM settings are not retroactive.
     * Any books downloaded prior will not have their settings changed.
     *
     * @param int $id
     * @param string $title
     * @param string $resource
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function updateBook(int $id, string $title, string $resource, array $optional = [])
    {
        $response =  $this
            ->httpClient()
            ->patch("book/$id", array_merge([
                'multipart' => [
                    [
                        'name'     => 'resource',
                        'contents' => $resource,
                        'filename' => 'book.' . $this->getResourceExtension($resource),
                    ],
                ],
                'query' => [
                    'title' => $title,
                    'drm' => $this->drm,
                ]
            ], $optional))
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Remove ebook.
     *
     * @param int $id
     * @return mixed
     * @throws GuzzleException
     */
    public function deleteBook(int $id)
    {
        $response =  $this
            ->httpClient()
            ->delete("book/{$id}");

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    /**
     * Generate download links for a specific title.
     * Links are then returned in an array in the number specified.
     *
     * @param int $id
     * @param int $count
     * @return mixed
     * @throws GuzzleException
     */
    public function generateBookLinks(int $id, int $count)
    {
        $response =  $this
            ->httpClient()
            ->post("book/{$id}/generate_links", [
                'query' => [
                    'links_count' => $count
                ]
            ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Emails the provided address with a download link for the eBook specified by its resource_id.
     * Full name is not required but recommended as it is shown in the e-mail when provided.
     *
     * @param string $resourceId
     * @param string $email
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function deliverBookLink(string $resourceId, string $email, array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->post('deliver-book-link', [
                'query' => array_merge([
                    'resource_id' => $resourceId,
                    'email' => $email
                ], $optional)
            ]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    /**
     * Emails the provided address with a download link for the eBook specified by its resource_id.
     * Full name is not required but recommended as it is shown in the e-mail when provided.
     *
     * @param array $bookList
     * @param string $email
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     * @example [['resource_id' => 'urn:uuid:cf5475bd-ac2a-4443-9809-71fd8211fd65', 'quantity' => 3]]
     *
     */
    public function deliverBookLinks(array $bookList, string $email, array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->post('deliver-book-links', [
                'query' => array_merge([
                    'book_list' => $bookList,
                    'email' => $email
                ], $optional)
            ]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    /**
     * This endpoint returns all eBooks fulfilled through their download link.
     * Downloads differ from transactions since a user may purchase a book, but not download it.
     *
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function download(array $optional = []): array
    {
        $response = $this
            ->httpClient()
            ->get('download', ['query' => $optional])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Get transaction by id.
     *
     * @param string $id
     * @return mixed
     * @throws GuzzleException
     */
    public function getTransaction(string $id)
    {
        $response = $this
            ->httpClient()
            ->get("transaction/{$id}")
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Create a list of transactions for all eBooks or a specific eBook via its resource_id.
     *
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function getTransactions(array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->get('transaction', ['query' => $optional])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Create a new transaction for a specific eBook called via its resource_id.
     * The response includes the download link for the eBook and whether download instructions should be included or not.
     * If set to false URL is a direct download.
     *
     * @param string $resourceId
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function createTransaction(string $resourceId, array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->post('transaction', [
                'query' => array_merge([
                    'resource_id' => $resourceId
                ], $optional)
            ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Update transaction.
     *
     * @param string $id
     * @param string $resourceId
     * @param bool $showInstructions
     * @param string $watermarkName
     * @param string $watermarkEmail
     * @param string $watermarkPhone
     * @param bool $watermarkPlaceBegin
     * @param bool $watermarkPlaceEnd
     * @param bool $watermarkPlaceRandom
     * @param string $watermarkPlaceRandomCount
     * @param string $usesRemaining
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function updateTransaction(
        string $id,
        string $resourceId,
        bool $showInstructions,
        string $watermarkName,
        string $watermarkEmail,
        string $watermarkPhone,
        bool $watermarkPlaceBegin,
        bool $watermarkPlaceEnd,
        bool $watermarkPlaceRandom,
        string $watermarkPlaceRandomCount,
        string $usesRemaining,
        array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->put("transaction/{$id}", [
                'query' => array_merge([
                    'resource_id' => $resourceId,
                    'show_instructions' => $showInstructions,
                    'watermark_name' => $watermarkName,
                    'watermark_email' => $watermarkEmail,
                    'watermark_phone' => $watermarkPhone,
                    'watermark_place_begin' => $watermarkPlaceBegin,
                    'watermark_place_end' => $watermarkPlaceEnd,
                    'watermark_place_random' => $watermarkPlaceRandom,
                    'watermark_place_random_count' => $watermarkPlaceRandomCount,
                    'uses_remaining' => $usesRemaining
                ], $optional)
            ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Delete transaction by id.
     *
     * @param string $id
     * @return mixed
     * @throws GuzzleException
     */
    public function deleteTransaction(string $id)
    {
        $response = $this
            ->httpClient()
            ->delete("transaction/{$id}");

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    /**
     * Get details of a master link.
     *
     * @param string $id
     * @return mixed
     * @throws GuzzleException
     */
    public function getMasterLink(string $id)
    {
        $response = $this
            ->httpClient()
            ->get("master_link/{$id}")
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Get a list of all master links on an account.
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function getMasterLinks()
    {
        $response = $this
            ->httpClient()
            ->get('master_link')
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Create a new master link for a specific eBook called via its resource_id with given optional expiry settings: number of uses or expiry date.
     * The response includes the master link that creates a new transaction each time it's used and yields a new ACSM file.
     *
     * @param string $resourceId
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function createMasterLink(string $resourceId, array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->post('master_link', [
                'query' => array_merge([
                    'resource_id' => $resourceId
                ], $optional)
            ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Partially updates a master link.
     *
     * @param string $id
     * @param string $resourceId
     * @param array $optional
     * @return mixed
     * @throws GuzzleException
     */
    public function updateMasterLink(string $id, string $resourceId, array $optional = [])
    {
        $response = $this
            ->httpClient()
            ->patch("master_link/{$id}", [
                'query' => array_merge([
                    'resource_id' => $resourceId
                ], $optional)
            ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * Deletes a master link, effectively rendering it unusable.
     *
     * @param string $id
     * @return mixed
     * @throws GuzzleException
     */
    public function deleteMasterLink(string $id)
    {
        $response = $this
            ->httpClient()
            ->delete("master_link/{$id}");

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    /**
     * Http client with base settings.
     *
     * @return Client
     */
    private function httpClient()
    {
        return new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Authorization' => "Token {$this->apiToken}"
            ],
            'verify' => false
        ]);
    }

    /**
     * Get resource extension.
     *
     * @param string $resource
     * @return mixed
     */
    private function getResourceExtension(string $resource)
    {
        $mime = (new finfo(FILEINFO_MIME_TYPE))
            ->buffer($resource);

        $ext = explode('/', $mime)[1] ?? null;
        $ext = explode('+', $ext)[0] ?? null;

        return $ext;
    }
}
