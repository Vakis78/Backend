<?php
header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $product_id=$_GET["product_id"];

    $subproducts=R::getAll("SELECT sp.id,sp.subproduct_name,sp.required FROM subproduct sp,products_group pg WHERE pg.subproduct_id=sp.id AND pg.product_id=?",array($product_id));
    // $subproducts=R::getAll("SELECT * FROM subproduct WHERE product_id=? ORDER BY subproduct_name",array($product_id));
    for ($a=0;$a<count($subproducts);$a++){
        $sp=$subproducts[$a];
        $subproductoptions=R::getAll("SELECT * FROM subproductoption WHERE subproduct_id=? ORDER BY option_name",array($sp["id"]));
        $sp["options"]=$subproductoptions;
        $subproducts[$a]=$sp;
    }
    $result=array("status"=>200,"subproducts"=>$subproducts);
    goto output;
}catch(Exception $ex){
    $result=array("status"=>500,"msg"=>$ex->getMessage());
    goto output;
}
output:
closeDbConnection();
echo json_encode($result);
exit();
?>