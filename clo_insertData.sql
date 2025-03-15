/*Insert Data*/

delete from clo_outfit_items;
delete from clo_outfits;
delete from clo_clothing_items;
delete from clo_user;

call clo_create_user("alice123", "password");
call clo_create_user("bob_smith", "123");


select * from clo_user;