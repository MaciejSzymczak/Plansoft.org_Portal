SELECT CLASSES.Id
, to_char(DAY,'yyyy-mm-dd') ||':'||GRIDS.CAPTION  ||
' Przedmiot:' ||SUB.NAME ||
' Forma:' || frm.Name ||
' Wykładowcy:' || calc_lecturers
--	(select listagg(title ||' '||first_name||' '||last_name,'; ') WITHIN GROUP (ORDER BY last_name)
--	 from lecturers 
--	 where id in (select lec_id from lec_cla where cla_id=classes.id)) 
|| 
' Grupy:' || calc_groups
--	(select listagg(nvl(g.name, g.abbreviation),'; ') WITHIN GROUP (ORDER BY nvl(g.name, g.abbreviation))
--	 from groups g 
--	 where id in (select gro_id from gro_cla where cla_id=classes.id)) 
|| 
' Zasoby:'  || calc_rooms
--	(select listagg(r.name||' '||r.attribs_01,'; ') WITHIN GROUP (ORDER BY r.name||' '||r.attribs_01)
--	 from rooms r 
--	 where id in (select rom_id from rom_cla where cla_id=classes.id))
RECORD_NAME
, to_char(DAY,'yyyy-mm-dd') DAY
, to_char(DAY,'dy', 'NLS_DATE_LANGUAGE = polish') DAY_OF_WEEK
, GRIDS.CAPTION HOUR
, SUB.NAME "Przedmiot"
, frm.Name "Forma"
, calc_lecturers
, calc_groups
, calc_rooms
--, (select listagg(title ||' '||first_name||' '||last_name,'; ') WITHIN GROUP (ORDER BY last_name)
-- from lecturers 
-- where id in (select lec_id from lec_cla where cla_id=classes.id))
--, (select listagg(nvl(g.name, g.abbreviation),'; ') WITHIN GROUP (ORDER BY nvl(g.name, g.abbreviation))
-- from groups g 
-- where id in (select gro_id from gro_cla where cla_id=classes.id))
--, (select listagg(r.name||' '||r.attribs_01,'; ') WITHIN GROUP (ORDER BY r.name||' '||r.attribs_01)
-- from rooms r 
-- where id in (select rom_id from rom_cla where cla_id=classes.id))
, GRIDS.DURATION * (FILL/100) --"zajecia"
, CLASSES.DESC2 --"Info dla studentow"
, CLASSES.OWNER
--check if user can plan (edit/delete) classes on this day
, (select count(1) from res_hints where day=CLASSES.DAY and hour=CLASSES.HOUR and res_id=:confineCalId and rownum=1) CAN_MODIFY
FROM CLASSES
,GRIDS
,SUBJECTS SUB
,org_units sou
,FORMS FRM
WHERE FOR_ID = FRM.ID
 and SUB.ID (+)= SUB_ID
 and CLASSES.HOUR = GRIDS.NO
 and SUB.ORGUNI_ID = sou.ID(+)
 --and CLASSES.ID in (SELECT CLA_ID FROM LEC_CLA WHERE LEC_ID =:LEC_ID)
 and (CLASSES.DAY, CLASSES.HOUR) in (select day, hour from res_hints where res_id=:confineCalId)
 and (classes.id in ((select cla_id from lec_cla where lec_id in (select id from lecturers where LECTURERS.ID IN (SELECT LEC_ID FROM LEC_PLA WHERE PLA_ID = :PLA_ID) )) /*union all select id from classes where calc_lec_ids is null*/)
   or classes.id in ((select cla_id from gro_cla where gro_id in (select id from groups    where GROUPS.ID    IN (SELECT GRO_ID FROM GRO_PLA WHERE PLA_ID = :PLA_ID) ))) /*union all select id from classes where calc_gro_ids is null)*/
   or classes.id in ((select cla_id from rom_cla where rom_id in (select id from rooms     where ROOMS.ID     IN (SELECT ROM_ID FROM ROM_PLA WHERE PLA_ID = :PLA_ID) )))) /*union all select id from classes where calc_rom_ids is null)*/
 --and (classes.sub_id in (select id from subjects  where SUBJECTS.ID IN (SELECT SUB_ID FROM SUB_PLA WHERE PLA_ID = :PLA_ID)) or classes.sub_id is null)
 --and (classes.for_id in (select id from forms     where FORMS.ID IN (SELECT FOR_ID FROM FOR_PLA WHERE PLA_ID = :PLA_ID)) or classes.for_id is null )
order by classes.day desc, CLASSES.HOUR