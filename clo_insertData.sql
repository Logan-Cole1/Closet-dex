/*Insert Data*/

-- delete from clo_outfit_items;
-- delete from clo_outfits;
delete from clo_clothing_items;
delete from clo_user;

call clo_create_user("alice123", "password");
call clo_create_user("bob_smith", "123");

-- all clo_insert_clothing("alice123", "Blue Jeans", "Bottom", "https://th.bing.com/th/id/OIP.24tX4kQXtsFVNxlmulgyvAHaLH?rs=1&pid=ImgDetMain");
-- call clo_insert_clothing("alice123", "Leather Jacket", "Outerwear", "https://happygentleman.com/wp-content/uploads/2019/11/uclass-b921-mens-leather-jacket-black-3_1.jpg");
-- call clo_insert_clothing("bob_smith", "Running Shoes", "Footwear", "https://shop.mynavyexchange.com/products/images/xlarge/13355569_023.jpg");

-- call clo_create_outfit("alice123", "Casual Outfit", null);
-- call clo_create_outfit("bob_smith", "Workout Attire", null);

-- call clo_insert_outfit_item("alice123", "Casual Outfit", "Blue Jeans");
-- call clo_insert_outfit_item("alice123", "Casual Outfit", "Leather Jacket");
-- call clo_insert_outfit_item("bob_smith", "Workout Attire", "Running Shoes");


select * from clo_user;
select * from clo_clothing_items;
-- select * from clo_outfits;
-- select * from clo_outfit_items;