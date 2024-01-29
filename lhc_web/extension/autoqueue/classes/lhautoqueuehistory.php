<?php

class erLhcoreClassModelAutoqueueHistory {

    public function todayHistory() {
        $this->time_from = self::getTodayTimestamp();
        return $this->doSearch();
    }

    public function setTimeHistory($time_from = null, $time_to = null) {
        $this->time_from = $time_from;
        $this->time_to   = $time_to;
        return $this->doSearch();
    }

    private function doSearch() {
        $db = ezcDbInstance::get();

        try {
            $sqlOptions = "";

            if (isset($this->time_from))
                $sqlOptions .= "AND fr.time >= " . $this->time_from;
            if (isset($this->time_to))
                $sqlOptions .= "AND fr.time <= " . $this->time_to;

            $sql = "SELECT fr.*, ua.name, ua.surname, up.name AS namepro, up.surname AS surnamepro, c.nick FROM fila_redir fr LEFT OUTER JOIN lh_users ua ON fr.user_id_ant = ua.id LEFT OUTER JOIN lh_users up ON fr.user_id_pro = up.id LEFT OUTER JOIN lh_chat c ON fr.chat_id = c.id WHERE 1=1 {$sqlOptions} ORDER BY fr.tslasign DESC";

            $stmt = $db->prepare($sql);
            //$stmt->bindValue(':motivo', self::MOTIVO_ATRASO_ACEITE, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

            //$chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $params['chat']->id);

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public function doCreate() {
        $db = ezcDbInstance::get();

        try {
            $sql = "INSERT INTO fila_redir (chat_id, user_id_ant, user_id_pro, motivo, time, tslasign) VALUES (:chat_id, :user_id_ant, :user_id_pro, :motivo, :time, :tslasign)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':chat_id', $this->chat_id, PDO::PARAM_INT);

            if (isset($this->user_id_ant))
                $stmt->bindValue(':user_id_ant', $this->user_id_ant, PDO::PARAM_INT);
            else
                $stmt->bindValue(':user_id_ant', $this->user_id_ant, PDO::PARAM_NULL);

            $stmt->bindValue(':user_id_pro', $this->user_id_pro, PDO::PARAM_INT);
            $stmt->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
            $stmt->bindValue(':time', $this->time, PDO::PARAM_INT);
            $stmt->bindValue(':tslasign', $this->tslasign, PDO::PARAM_INT);

            $stmt->execute();

            $db->commit();

            //$chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $params['chat']->id);

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public static function getMotivoString($motivo) {
        switch ($motivo) {
            case self::MOTIVO_ATRASO_ACEITE:
                return 'Tempo limite excedido (1m30s)';
            case self::MOTIVO_RETOMADA_ATEND:
                return 'Retomada de atendimento anterior < 1h';
            default:
                return 'Erro ao recuperar o motivo';
        }
    }

    public static function getTodayTimestamp() {
        $today = new DateTime();
        $today->setTime(0, 0);

        return $today->getTimestamp(); //recupera a timestamp de 00:00 do dia atual
    }

    //atributos da tabela
    public $id;
    public $chat_id;
    public $user_id_ant;
    public $user_id_pro;
    public $motivo;
    public $time;
    public $tslasign;

    //constantes
    const MOTIVO_ATRASO_ACEITE = 'Atr';
    const MOTIVO_RETOMADA_ATEND = 'Ret';

    //atributos de controle
    private $time_from; //Desde que tempo deseja procurar
    private $time_to;   //Até que tempo deseja procurar
}

?>