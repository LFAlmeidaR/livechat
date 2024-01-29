-- Tabela para guardar os dados do histórico de redirecionamento
CREATE TABLE fila_redir (
	id 			INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    chat_id 	INT(11) NOT NULL,
    user_id_ant	INT(11),
    user_id_pro INT(11) NOT NULL,
    motivo		ENUM('Atr', 'Ret'), -- Atraso em aceitar, Retomada de atendimento
    time		INT(11),
    tslasign	INT(11),
    
    INDEX idx_chat_id (chat_id),
    INDEX idx_user_id_ant (user_id_ant),
    INDEX idx_user_id_pro (user_id_pro)
    
    -- Constraints fornecidas a titulo de conhecimento. O chat NÃO funciona corretamente com elas.
    -- CONSTRAINT FK_CHAT_ID FOREIGN KEY (CHAT_ID) REFERENCES LH_CHAT(ID),
    -- CONSTRAINT FK_USER_ID_ANT FOREIGN KEY (USER_ID_ANT) REFERENCES LH_USERS(ID),
    -- CONSTRAINT FK_USER_ID_PRO FOREIGN KEY (USER_ID_PRO) REFERENCES LH_USERS(ID)
);

-- Exemplo de insert
-- INSERT INTO fila_redir VALUES (1,11260,29,33,'Atr',1538017200,1538017200);

-- Exemplo de select para o relatorio
-- SELECT FR.*, UA.NAME, UA.SURNAME, UP.NAME AS NAMEPRO, UP.SURNAME AS SURNAMEPRO, C.NICK 
-- 	 FROM FILA_REDIR FR INNER JOIN LH_USERS UA ON FR.USER_ID_ANT = UA.ID 
-- 					    INNER JOIN LH_USERS UP ON FR.USER_ID_PRO = UP.ID 
-- 					    INNER JOIN LH_CHAT C ON FR.CHAT_ID = C.ID 
-- 	 WHERE FR.TIME > 1538017200 ORDER BY FR.TIME ASC;