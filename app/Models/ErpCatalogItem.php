<?php

namespace App\Models;

/**
 * Class ErpCatalogItem
 * Alias / subclass of ErpStoreCatalog for seamless backwards-compatibility.
 */
class ErpCatalogItem extends ErpStoreCatalog
{
    protected $table = 'erp_store_catalog';
}
