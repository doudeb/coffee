Update elgg_entities ent
Inner Join elgg_objects_entity ent_obj On ent.guid = ent_obj.guid
Set ent_obj.description = concat (ent_obj.title, ' ', ent_obj.description)
Where ent.subtype = 8;

Create Temporary Table Comment
Select ent.guid
	, group_concat(annotation_value.string separator ' ') as text
From elgg_entities ent
Inner Join elgg_objects_entity ent_obj On ent.guid = ent_obj.guid
Inner Join elgg_annotations On ent.guid = elgg_annotations.entity_guid
Inner Join elgg_metastrings annotation_value On elgg_annotations.value_id = annotation_value.id
Where ent.subtype = 8
Group by ent.guid;


Update elgg_entities ent
Inner Join elgg_objects_entity ent_obj On ent.guid = ent_obj.guid
Inner Join Comment On ent.guid = Comment.guid
Set ent_obj.description = concat (ent_obj.description, ' ', Comment.text);


Alter Table elgg_users_apisessions engine= MYISAM;
Drop Table Comment;