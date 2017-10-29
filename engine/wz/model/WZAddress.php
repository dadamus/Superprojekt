<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 15.10.2017
 * Time: 19:59
 */

/**
 * Class WZAddress
 */
class WZAddress
{
    /**
     * @var int
     */
    private $id = 0;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $address2 = "";

    /**
     * @var string
     */
    private $nip;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAddress1(): string
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     */
    public function setAddress1(string $address1)
    {
        $this->address1 = $address1;
    }

    /**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     */
    public function setAddress2(string $address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getNip(): string
    {
        return $this->nip;
    }

    /**
     * @param string $nip
     */
    public function setNip(string $nip)
    {
        $this->nip = $nip;
    }

    public function save()
    {
        global $db;
        $saveQuery = new sqlBuilder(sqlBuilder::INSERT, 'wz_address');

        if ($this->getId() > 0) {
            $saveQuery = new sqlBuilder(sqlBuilder::UPDATE, 'wz_address');
            $saveQuery->addCondition('id = ' . $this->getId());
        }

        $saveQuery->bindValue('address_name', $this->getName(), PDO::PARAM_STR);
        $saveQuery->bindValue('address1', $this->getAddress1(), PDO::PARAM_STR);
        $saveQuery->bindValue('address2', $this->getAddress2(), PDO::PARAM_STR);
        $saveQuery->bindValue('nip', $this->getNip(), PDO::PARAM_STR);
        $saveQuery->flush();

        if ($this->getId() === 0) {
            $this->setId($db->lastInsertId());
        }
    }

    /**
     * @param int $id
     */
    public function getById(int $id)
    {
        global $db;


        $addressQuery = $db->prepare("SELECT * FROM wz_address WHERE id = :id");
        $addressQuery->bindValue(':id', $id, PDO::PARAM_INT);
        $addressQuery->execute();

        $addressData = $addressQuery->fetch();

        $this->setId($id);
        $this->setName($addressData['address_name']);
        $this->setAddress1($addressData['address1']);
        $this->setAddress2($addressData['address2']);
        $this->setNip($addressData['nip']);
    }
}