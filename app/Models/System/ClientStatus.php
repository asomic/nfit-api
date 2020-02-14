<?php

namespace App\Models\System\Clients;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesSystemConnection;

class ClientStatus extends Model
{
    use UsesSystemConnection;

    /**
	 * Satus User
	 */
    const SIN_ESTADO = 0;

    /**
	 * Satus User
	 */
    const ACTIVO = 1;

    /**
     * Satus User
     */
    const INACTIVO = 2;

    /**
     * Satus User
     */
    const PRUEBA = 3;

    /**
     * [$type description]
     *
     * @var array
     */
    protected $types = [
        0 => 'primary',
        1 => 'success',
        2 => 'danger',
        3 => 'warning'
    ];

    /**
     * [$typeName description]
     *
     * @var array
     */
    protected $typeNames = [
        0 => 'TODOS',
        1 => 'ACTIVO',
        2 => 'INACTIVO',
        3 => 'PRUEBA'
    ];

    /**
     * [getType description]
     *
     * @param   integer  $index
     *
     * @return  string
     */
    public function getType($index)
    {
        return $this->types[$index];
    }

    /**
     * [getType description]
     *
     * @param   integer  $index
     *
     * @return  string
     */
    public function AllTypes()
    {
        return $this->types;
    }

    /**
     * [getTypeName description]
     *
     * @param   integer  $index
     *
     * @return  string
     */
    public function getTypeName($index)
    {
        return $this->typeNames[$index];
    }

    /**
     * [getType description]
     *
     * @param   integer  $index
     *
     * @return  string
     */
    public function AllTypeNames()
    {
        return $this->typeNames;
    }
}
