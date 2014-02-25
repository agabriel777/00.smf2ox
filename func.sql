DELIMITER $$

USE `vaspun`$$

DROP FUNCTION IF EXISTS `getLastReadInSmf`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getLastReadInSmf`(puid INT, ptid INT) RETURNS INT(11)
    READS SQL DATA
BEGIN
      DECLARE last_read INT;
      DECLARE owtid INT;
      DECLARE owuid INT;

      SELECT id_msg
      INTO last_read
      FROM smf_log_topics l
      WHERE id_member = (SELECT id_member FROM smf_members WHERE ow_id=puid)
      AND id_topic = (SELECT id_topic FROM smf_topics WHERE ow_id=ptid);

      IF last_read IS NOT NULL THEN 
			SELECT MAX(ow_id)
			INTO last_read
			  FROM smf_messages
			  WHERE id_topic = (SELECT id_topic FROM smf_topics WHERE ow_id=ptid) AND id_msg <= last_read;
          
      ELSE 
			SELECT MAX(ow_id)
			INTO last_read
			  FROM vaspun.smf_messages m 
			 WHERE m.id_topic = (SELECT id_topic FROM smf_topics WHERE ow_id=ptid)
			   AND m.id_msg<=(SELECT id_msg FROM smf_log_mark_read mr WHERE mr.id_board=m.id_board AND mr.id_member = (SELECT id_member FROM smf_members WHERE ow_id=puid));
      END IF;

       RETURN last_read;
    END$$

DELIMITER ;