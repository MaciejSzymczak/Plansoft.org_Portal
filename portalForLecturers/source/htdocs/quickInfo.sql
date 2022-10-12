SELECT CLASSES.Id
, to_char(DAY,'yyyy-mm-dd') DAY
, to_char(DAY,'dy', 'NLS_DATE_LANGUAGE = polish') DAY_OF_WEEK
, GRIDS.CAPTION HOUR
, SUB.NAME "Przedmiot"
, frm.Name "Forma"
, calc_lecturers
, calc_groups
, calc_rooms
/*
, (select listagg(title ||' '||first_name||' '||last_name,'; ') WITHIN GROUP (ORDER BY last_name)
 from lecturers 
 where id in (select lec_id from lec_cla where cla_id=classes.id))
, (select listagg(nvl(g.name, g.abbreviation),'; ') WITHIN GROUP (ORDER BY nvl(g.name, g.abbreviation))
 from groups g 
 where id in (select gro_id from gro_cla where cla_id=classes.id))
, (select listagg(r.name||' '||r.attribs_01,'; ') WITHIN GROUP (ORDER BY r.name||' '||r.attribs_01)
 from rooms r 
 where id in (select rom_id from rom_cla where cla_id=classes.id))
*/
, GRIDS.DURATION * (FILL/100) --"zajecia"
, CLASSES.DESC2 --"Info dla studentow"
, CLASSES.OWNER
FROM CLASSES
,GRIDS
,SUBJECTS SUB
,org_units sou
,FORMS FRM 
WHERE FOR_ID = FRM.ID
 --show classes only
 --and SUB.ID (+)= SUB_ID
 and SUB.ID= SUB_ID
 and CLASSES.HOUR = GRIDS.NO
 and SUB.ORGUNI_ID = sou.ID(+)
 and CLASSES.DAY between trunc(sysdate)+:daysFrom and trunc(sysdate)+:daysTo
 and (upper(calc_groups) like upper(:search)
 or upper(calc_groups)  like upper(:search)
 or upper(calc_rooms)  like upper(:search)
 or upper(sub.name)  like upper(:search)
 or upper(frm.name)  like upper(:search)
 or upper(to_char(DAY,'dy', 'NLS_DATE_LANGUAGE = polish'))  like upper(:search)
 or GRIDS.CAPTION  like upper(:search)
 or upper(CLASSES.DESC2) like upper(:search)
 or classes.id in (
		select cla_id 
		  from lec_cla 
		 where lec_id in (
			select id 
			  from lecturers 
			 where upper(title ||' '||first_name||' '||last_name) like upper(:search)
		 )
	)
 )
order by classes.day, CLASSES.HOUR





