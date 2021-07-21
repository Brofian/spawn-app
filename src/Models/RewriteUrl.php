<?php declare(strict_types=1);

namespace spawnApp\Models;

use spawn\system\Core\Base\Database\Query\QueryBuilder;
use spawn\system\Core\Base\Helper\DatabaseHelper;

class RewriteUrl {

    protected string $c_url;
    protected string $rewrite_url;

    public function __construct(string $c_url, string $rewrite_url)
    {
        $this->c_url = $c_url;
        $this->rewrite_url = $rewrite_url;
    }


    public static function loadAll(DatabaseHelper $dbHelper) {
        $result = $dbHelper->query("
            SELECT * FROM spawn_rewrite_urls
        ");

        $entities = [];
        foreach($result as $row) {
            $entities[] = self::resultToEntity($row);
        }

        return $entities;
    }

    public static function findSeoByReplacement(DatabaseHelper $dbHelper, string $replacementUrl): ?self {
        $qb = new QueryBuilder($dbHelper->getConnection());
        $erg = $qb->select('*')
            ->from('spawn_rewrite_urls')
            ->where('replacement_url', $replacementUrl)
            ->limit(1)
            ->execute();

        if(!is_array($erg) || !isset($erg[0])) {
            return null;
        }

        return self::resultToEntity($erg[0]);
    }

    public static function resultToEntity($result) : self {

        $url = new RewriteUrl(
            $result['c_url'],
            $result['replacement_url']
        );

        return $url;
    }



    public function getCUrl(): ?string
    {
        return $this->c_url;
    }

    public function setCUrl(string $c_url): self {
        $this->c_url = $c_url;
        return $this;
    }

    public function getRewriteUrl(): ?string
    {
        return $this->rewrite_url;
    }

    public function setRewriteUrl(string $rewrite_url): self {
        $this->rewrite_url = $rewrite_url;
        return $this;
    }


}