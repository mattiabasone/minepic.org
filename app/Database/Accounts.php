<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Accounts
 * @package App\Database
 *
 * Table fields
 *
 * @property int $id
 * @property string $uuid
 * @property string $username
 * @property int $fail_count
 * @property string $skin
 * @property string $cape
 * @property string $created_at
 * @property string $updated_at
 */
class Accounts extends Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * @var bool
     */
    public $timestamps = true;
}