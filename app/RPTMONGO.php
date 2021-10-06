<?php

namespace App;
use Jenssegers\Mongodb\Eloquent\Model;

class RPTMONGO extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'reports';

    protected $dates = ['reportDate', 'createdDate', 'updatedDate'];

}
