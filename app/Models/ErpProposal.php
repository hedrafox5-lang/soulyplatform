<?php

namespace App\Models;

/**
 * Class ErpProposal
 * Alias / subclass of ErpSalesProposal for seamless backwards-compatibility.
 */
class ErpProposal extends ErpSalesProposal
{
    protected $table = 'erp_sales_proposals';
}
