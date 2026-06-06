<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Repositories;

interface ShortLinkRepositoryInterface
{
    /**
     * Create a new shortlink record.
     *
     * @param string $url      The target URL
     * @param string $linkId   The associated link ID
     * @param string $code     The generated short code
     * @return array           The created shortlink record
     */
    public function create(string $url, string $linkId, string $code): array;

    /**
     * Find a shortlink by its short code.
     *
     * @param string $code
     * @return array|null
     */
    public function findByCode(string $code): ?array;

    /**
     * Find a shortlink by the associated link ID.
     *
     * @param string $linkId
     * @return array|null
     */
    public function findByLinkId(string $linkId): ?array;

    /**
     * Delete a shortlink (and optionally cascade events).
     *
     * @param string $shortlinkId
     * @return void
     */
    public function delete(string $shortlinkId): void;

    /**
     * Delete a shortlink by its associated link ID.
     *
     * @param string $linkId
     * @return void
     */
    public function deleteByLinkId(string $linkId): void;

    /**
     * Return all shortlinks (useful for debugging or admin UI).
     *
     * @return array
     */
    public function all(): array;
}
