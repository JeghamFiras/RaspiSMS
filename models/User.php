<?php
	namespace models;
	class User extends \Model
    {
        /**
         * Retourne un user par son email
         * @param string $email : L'email du user
         * @return mixed array | false : false si pas de user pour ce mail, sinon le user associé sous forme de tableau
         */
        public function get_by_email ($email)
        {
            return $this->select_one('user', ['email' => $email]);
        }

		/**
         * Return list of user
         * @param int $limit : Number of user to return
         * @param int $offset : Number of user to skip
		 */
		public function list ($limit, $offset)
        {
            return $this->select('user', [], '', false, $limit, $offset);
		}
        
        /**
		 * Retourne une liste de useres sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à supprimer
         * @return int : Le nombre de lignes supprimées
		 */
        public function remove ($id)
        {
            return $this->delete('user', ['id' => $id]);
        }

        /**
         * Insert un user
         * @param array $user : La user à insérer avec les champs name, script, admin & admin
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle lignée insérée
         */
        public function insert ($user)
        {
            $result = $this->insertIntoTable('user', $user);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }

        /**
         * Met à jour un user par son id
         * @param int $id : L'id de la user à modifier
         * @param array $user : Les données à mettre à jour pour la user
         * @return int : le nombre de ligne modifiées
         */
        public function update ($id, $user)
        {
            return $this->updateTableWhere('user', $user, ['id' => $id]);
        }
        
        /**
         * Update a user password by his id
         * @param int $id : User id
         * @param array $password : The new password of the user
         * @return int : Number of modified lines
         */
        public function update_password ($id, $password)
        {
            return $this->update('user', ['password' => $password], ['id' => $id]);
        }
        
        /**
         * Update a user transfer property value by his id
         * @param int $id : User id
         * @param array $transfer : The new transfer property value
         * @return int : Number of modified lines
         */
        public function update_transfer ($id, $transfer)
        {
            return $this->update('user', ['transfer' => $transfer], ['id' => $id]);
        }
        
        /**
         * Update a user email by his id
         * @param int $id : User id
         * @param array $email : The new email
         * @return int : Number of modified lines
         */
        public function update_email ($id, $email)
        {
            return $this->update('user', ['email' => $email], ['id' => $id]);
        }
    }