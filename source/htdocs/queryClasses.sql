SELECT CLASSES.Id
, to_char(DAY,'yyyy-mm-dd') ||':'||GRIDS.CAPTION  ||
' Przedmiot:' ||SUB.NAME ||
' Forma:' || frm.Name ||
' Wykładowcy:' || SUBSTR(lecturers.full_name,1,254) || 
' Grupy:' || SUBSTR(groups.group_name,1,254)  || 
' Zasoby:'  || SUBSTR(resources.res_name,1,254) 
RECORD_NAME
, to_char(DAY,'yyyy-mm-dd') DAY
, to_char(DAY,'dy') DAY_OF_WEEK
, GRIDS.CAPTION HOUR
, SUB.NAME "Przedmiot"
, frm.Name "Forma"
, SUBSTR(lecturers.full_name,1,254) 
, SUBSTR(groups.group_name,1,254) 
, SUBSTR(resources.res_name,1,254) 
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
,(select trim(';'  from max(v1)||';'||max(v2)||';'||max(v3)||';'||max(v4)||';'||max(v5)||';'||max(v6)||';'||max(v7)||';'||max(v8)||';'||max(v9)||';'||max(v10)) full_name
     , trim(';' from max(u1)||';'||max(u2)||';'||max(u3)||';'||max(u4)||';'||max(u5)||';'||max(u6)||';'||max(u7)||';'||max(u8)||';'||max(u9)||';'||max(u10)) orguni
     , cla_id Id
from
(
select case when (row_number() over (partition by cla_id  order by lec_id))=1 then title ||' '||first_name||' '||last_name else null end v1
     , case when (row_number() over (partition by cla_id  order by lec_id))=2 then title ||' '||first_name||' '||last_name else null end v2
     , case when (row_number() over (partition by cla_id  order by lec_id))=3 then title ||' '||first_name||' '||last_name else null end v3
     , case when (row_number() over (partition by cla_id  order by lec_id))=4 then title ||' '||first_name||' '||last_name else null end v4
     , case when (row_number() over (partition by cla_id  order by lec_id))=5 then title ||' '||first_name||' '||last_name else null end v5
     , case when (row_number() over (partition by cla_id  order by lec_id))=6 then title ||' '||first_name||' '||last_name else null end v6
     , case when (row_number() over (partition by cla_id  order by lec_id))=7 then title ||' '||first_name||' '||last_name else null end v7
     , case when (row_number() over (partition by cla_id  order by lec_id))=8 then title ||' '||first_name||' '||last_name else null end v8
     , case when (row_number() over (partition by cla_id  order by lec_id))=9 then title ||' '||first_name||' '||last_name else null end v9
     , case when (row_number() over (partition by cla_id  order by lec_id))=10 then title ||' '||first_name||' '||last_name else null end v10
     , case when (row_number() over (partition by cla_id  order by lec_id))=1 then o.name else null end u1
     , case when (row_number() over (partition by cla_id  order by lec_id))=2 then o.name else null end u2
     , case when (row_number() over (partition by cla_id  order by lec_id))=3 then o.name else null end u3
     , case when (row_number() over (partition by cla_id  order by lec_id))=4 then o.name else null end u4
     , case when (row_number() over (partition by cla_id  order by lec_id))=5 then o.name else null end u5
     , case when (row_number() over (partition by cla_id  order by lec_id))=6 then o.name else null end u6
     , case when (row_number() over (partition by cla_id  order by lec_id))=7 then o.name else null end u7
     , case when (row_number() over (partition by cla_id  order by lec_id))=8 then o.name else null end u8
     , case when (row_number() over (partition by cla_id  order by lec_id))=9 then o.name else null end u9
     , case when (row_number() over (partition by cla_id  order by lec_id))=10 then o.name else null end u10
     , cla_id 
  from lec_cla
      , lecturers 
      , org_units o
  where lec_cla.lec_id = lecturers.id
    and o.id(+) = lecturers.orguni_id
	and cla_id in ((select cla_id from lec_cla where lec_id in (select id from lecturers where LECTURERS.ID IN (SELECT LEC_ID FROM LEC_PLA WHERE PLA_ID = :PLA_ID) )) /*union all select id from classes where calc_lec_ids is null*/)
)
group by cla_id 
) lecturers 
,(select cla_id id
     , trim(';' from max(n1)||';'||max(n2)||';'||max(n3)||';'||max(n4)||';'||max(n5)||';'||max(n6)||';'||max(n7)||';'||max(n8)||';'||max(n9)||';'||max(n10)) group_name
     , trim(';' from max(v1)||';'||max(v2)||';'||max(v3)||';'||max(v4)||';'||max(v5)||';'||max(v6)||';'||max(v7)||';'||max(v8)||';'||max(v9)||';'||max(v10)) group_type_dsp
     , trim(';' from max(u1)||';'||max(u2)||';'||max(u3)||';'||max(u4)||';'||max(u5)||';'||max(u6)||';'||max(u7)||';'||max(u8)||';'||max(u9)||';'||max(u10)) orguni
     , sum ( to_number(number_of_peoples) ) nop
from
(
select cla_id
     , to_number(number_of_peoples) number_of_peoples
     , case when (row_number() over (partition by cla_id  order by gro_id))=1 then nvl(g.name, g.abbreviation) else null end n1
     , case when (row_number() over (partition by cla_id  order by gro_id))=2 then nvl(g.name, g.abbreviation) else null end n2
     , case when (row_number() over (partition by cla_id  order by gro_id))=3 then nvl(g.name, g.abbreviation) else null end n3
     , case when (row_number() over (partition by cla_id  order by gro_id))=4 then nvl(g.name, g.abbreviation) else null end n4
     , case when (row_number() over (partition by cla_id  order by gro_id))=5 then nvl(g.name, g.abbreviation) else null end n5
     , case when (row_number() over (partition by cla_id  order by gro_id))=6 then nvl(g.name, g.abbreviation) else null end n6
     , case when (row_number() over (partition by cla_id  order by gro_id))=7 then nvl(g.name, g.abbreviation) else null end n7
     , case when (row_number() over (partition by cla_id  order by gro_id))=8 then nvl(g.name, g.abbreviation) else null end n8
     , case when (row_number() over (partition by cla_id  order by gro_id))=9 then nvl(g.name, g.abbreviation) else null end n9
     , case when (row_number() over (partition by cla_id  order by gro_id))=10 then nvl(g.name, g.abbreviation) else null end n10
     , case when (row_number() over (partition by cla_id  order by gro_id))=1 then substr(meaning,1,50) else null end v1
     , case when (row_number() over (partition by cla_id  order by gro_id))=2 then substr(meaning,1,50) else null end v2
     , case when (row_number() over (partition by cla_id  order by gro_id))=3 then substr(meaning,1,50) else null end v3
     , case when (row_number() over (partition by cla_id  order by gro_id))=4 then substr(meaning,1,50) else null end v4
     , case when (row_number() over (partition by cla_id  order by gro_id))=5 then substr(meaning,1,50) else null end v5
     , case when (row_number() over (partition by cla_id  order by gro_id))=6 then substr(meaning,1,50) else null end v6
     , case when (row_number() over (partition by cla_id  order by gro_id))=7 then substr(meaning,1,50) else null end v7
     , case when (row_number() over (partition by cla_id  order by gro_id))=8 then substr(meaning,1,50) else null end v8
     , case when (row_number() over (partition by cla_id  order by gro_id))=9 then substr(meaning,1,50) else null end v9
     , case when (row_number() over (partition by cla_id  order by gro_id))=10 then substr(meaning,1,50) else null end v10
     , case when (row_number() over (partition by cla_id  order by gro_id))=1 then gou.name else null end u1
     , case when (row_number() over (partition by cla_id  order by gro_id))=2 then gou.name else null end u2
     , case when (row_number() over (partition by cla_id  order by gro_id))=3 then gou.name else null end u3
     , case when (row_number() over (partition by cla_id  order by gro_id))=4 then gou.name else null end u4
     , case when (row_number() over (partition by cla_id  order by gro_id))=5 then gou.name else null end u5
     , case when (row_number() over (partition by cla_id  order by gro_id))=6 then gou.name else null end u6
     , case when (row_number() over (partition by cla_id  order by gro_id))=7 then gou.name else null end u7
     , case when (row_number() over (partition by cla_id  order by gro_id))=8 then gou.name else null end u8
     , case when (row_number() over (partition by cla_id  order by gro_id))=9 then gou.name else null end u9
     , case when (row_number() over (partition by cla_id  order by gro_id))=10 then gou.name else null end u10
 from groups   g
    , gro_cla  c  
    , org_units  gou
    , lookups  l 
where g.id = c.gro_id    
  and g.orguni_id = gou.id(+)
  and l.lookup_type(+) = 'GROUP_TYPE' 
  and l.code(+)  = g.group_type
  and cla_id in ((select cla_id from lec_cla where lec_id in (select id from lecturers where LECTURERS.ID IN (SELECT LEC_ID FROM LEC_PLA WHERE PLA_ID = :PLA_ID) )) /*union all select id from classes where calc_lec_ids is null*/)
)
group by cla_id
) groups 
,(select cla_id id
     , trim(';' from max(n1)||';'||max(n2)||';'||max(n3)||';'||max(n4)||';'||max(n5)||';'||max(n6)||';'||max(n7)||';'||max(n8)||';'||max(n9)||';'||max(n10)) res_name
     , trim(';' from max(u1)||';'||max(u2)||';'||max(u3)||';'||max(u4)||';'||max(u5)||';'||max(u6)||';'||max(u7)||';'||max(u8)||';'||max(u9)||';'||max(u10)) orguni
from
(
select cla_id
     , case when (row_number() over (partition by cla_id  order by rom_id))=1 then g.name||' '||g.attribs_01 else null end n1
     , case when (row_number() over (partition by cla_id  order by rom_id))=2 then g.name||' '||g.attribs_01 else null end n2
     , case when (row_number() over (partition by cla_id  order by rom_id))=3 then g.name||' '||g.attribs_01 else null end n3
     , case when (row_number() over (partition by cla_id  order by rom_id))=4 then g.name||' '||g.attribs_01 else null end n4
     , case when (row_number() over (partition by cla_id  order by rom_id))=5 then g.name||' '||g.attribs_01 else null end n5
     , case when (row_number() over (partition by cla_id  order by rom_id))=6 then g.name||' '||g.attribs_01 else null end n6
     , case when (row_number() over (partition by cla_id  order by rom_id))=7 then g.name||' '||g.attribs_01 else null end n7
     , case when (row_number() over (partition by cla_id  order by rom_id))=8 then g.name||' '||g.attribs_01 else null end n8
     , case when (row_number() over (partition by cla_id  order by rom_id))=9 then g.name||' '||g.attribs_01 else null end n9
     , case when (row_number() over (partition by cla_id  order by rom_id))=10 then g.name||' '||g.attribs_01 else null end n10
     , case when (row_number() over (partition by cla_id  order by rom_id))=1 then gou.name else null end u1
     , case when (row_number() over (partition by cla_id  order by rom_id))=2 then gou.name else null end u2
     , case when (row_number() over (partition by cla_id  order by rom_id))=3 then gou.name else null end u3
     , case when (row_number() over (partition by cla_id  order by rom_id))=4 then gou.name else null end u4
     , case when (row_number() over (partition by cla_id  order by rom_id))=5 then gou.name else null end u5
     , case when (row_number() over (partition by cla_id  order by rom_id))=6 then gou.name else null end u6
     , case when (row_number() over (partition by cla_id  order by rom_id))=7 then gou.name else null end u7
     , case when (row_number() over (partition by cla_id  order by rom_id))=8 then gou.name else null end u8
     , case when (row_number() over (partition by cla_id  order by rom_id))=9 then gou.name else null end u9
     , case when (row_number() over (partition by cla_id  order by rom_id))=10 then gou.name else null end u10
 from rooms   g
    , rom_cla  c
    , org_units  gou
where g.id = c.rom_id
  and g.orguni_id = gou.id(+)
  and cla_id in ((select cla_id from lec_cla where lec_id in (select id from lecturers where LECTURERS.ID IN (SELECT LEC_ID FROM LEC_PLA WHERE PLA_ID = :PLA_ID) )) /*union all select id from classes where calc_lec_ids is null*/)
)
group by cla_id
) resources 
WHERE lecturers.id(+) = classes.id
 and groups.id(+) = classes.id
 and resources.id(+) = classes.id
 and FOR_ID = FRM.ID
 and SUB.ID (+)= SUB_ID
 and CLASSES.HOUR = GRIDS.NO
 and SUB.ORGUNI_ID = sou.ID(+)
 --and CLASSES.ID in (SELECT CLA_ID FROM LEC_CLA WHERE LEC_ID =:LEC_ID)
 and (CLASSES.DAY, CLASSES.HOUR) in (select day, hour from res_hints where res_id=:confineCalId)
 and classes.id in ((select cla_id from lec_cla where lec_id in (select id from lecturers where LECTURERS.ID IN (SELECT LEC_ID FROM LEC_PLA WHERE PLA_ID = :PLA_ID) )) /*union all select id from classes where calc_lec_ids is null*/)
 --and classes.id in ((select cla_id from gro_cla where gro_id in (select id from groups    where GROUPS.ID IN (SELECT GRO_ID FROM GRO_PLA WHERE PLA_ID = :PLA_ID) )) union all select id from classes where calc_gro_ids is null)
 --and classes.id in ((select cla_id from rom_cla where rom_id in (select id from rooms     where ROOMS.ID IN (SELECT ROM_ID FROM ROM_PLA WHERE PLA_ID = :PLA_ID) )) union all select id from classes where calc_rom_ids is null)
 --and (classes.sub_id in (select id from subjects  where SUBJECTS.ID IN (SELECT SUB_ID FROM SUB_PLA WHERE PLA_ID = :PLA_ID)) or classes.sub_id is null)
 --and (classes.for_id in (select id from forms     where FORMS.ID IN (SELECT FOR_ID FROM FOR_PLA WHERE PLA_ID = :PLA_ID)) or classes.for_id is null )
order by classes.day desc