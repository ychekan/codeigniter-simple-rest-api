<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'auto_increment' => true,
                'unsigned' => true
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('role');
        $this->forge->createTable('roles');

        $this->db->table('roles')->insert(['role' => 'user', 'id' => 1]);
        $this->db->table('roles')->insert(['role' => 'admin', 'id' => 2]);


        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 200
            ],
            'role_id' => [
                'type' => 'INT',
                'default' => '1',
                'unsigned' => true
            ],
            'hash' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true
            ],
            'verified_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true
            ],
        ]);
        $this->forge->addKey('id', true);

        // Keys help optimize database performance; we'll add some for fields we are likely
        // to search or filter by
        $this->forge->addKey('email');
        $this->forge->addKey('username');

        // While not necessary, indexing against `deleted_at` is a good idea if your model
        // is using soft deletes, since most SELECT statements will include `deleted_at`
        $this->forge->addKey(['deleted_at', 'id']);

        $this->forge->addForeignKey('role_id', 'roles', 'id');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('roles', true);
    }
}
