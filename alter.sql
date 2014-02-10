ALTER TABLE smf_members ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE smf_boards ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE smf_categories ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE smf_topics ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE smf_messages ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE ow_forum_read_topic ADD `postId` MEDIUMINT(11) DEFAULT NULL;

UPDATE smf_topics
SET id_member_started = (SELECT id_member
FROM smf_messages m 
WHERE m.id_topic=id_topic
ORDER BY id_msg ASC
LIMIT 1)
WHERE id_member_started=0


-- dupa import

-- ultimul mesaj citit
INSERT INTO `ow_forum_read_topic` (`topicId`, `userId`, `postId`) 
SELECT t.`ow_id`, u.ow_id, m.ow_id
FROM smf_members u
LEFT JOIN smf_messages m ON m.`id_msg`=u.`id_msg_last_visit`
LEFT JOIN smf_topics t ON t.`id_topic`=m.`id_topic`
WHERE  id_msg_last_visit>0
AND m.`id_msg` IS NOT NULL;


