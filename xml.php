<?php 
//ALTER TABLE nombre_tabla AUTO_INCREMENT= 1
//http://www.grupocva.com/catalogo_clientes_xml/lista_precios.xml?cliente=26813&marca=%&grupo=%&clave=%&codigo=%porcentaje=10&tc=1&dc=1&dt=1
//http://www.grupocva.com/catalogo_clientes_xml/lista_precios.xml?cliente=25191&marca=%&grupo=%&clave=%&codigo=%&porcentaje=15&tc=1&dc=1&dt=1
set_time_limit(500);

$host="127.0.0.1";
$usuario="root";
$pass="";
$base="residencia";
//¨******EMPIEZA LA CONEXION¨******
class Conexion
{
private $_host; 
private $_pass;
private $_base;
private $_con;
 
public function hostdb($host, $base){
	$this->_host=$host;
	$this->_base=$base;
}
public function usuario($usuario, $pass){
	$this->_user=$usuario;
	$this->_pass=$pass;
}

public function con($conectar){
	if($conectar=='si'){
		$conexion=mysql_connect($this->_host, $this->_user, $this->_pass);
		mysql_select_db($this->_base, $conexion);
		$this->_con=$conexion;
		}
	else
		{
		echo "No se ha encontrado conexion a mysql";
		}
	}
	public function descon(){
		return $this->_con;
	}
//¨******TERMINA LA CONEXION¨******
	public function slug($string)
	 {	
	$characters = array(
		"Á" => "A", "Ç" => "c", "É" => "e", "Í" => "i", "Ñ" => "n", "Ó" => "o", "Ú" => "u", 
		"á" => "a", "ç" => "c", "é" => "e", "í" => "i", "ñ" => "n", "ó" => "o", "ú" => "u",
		"à" => "a", "è" => "e", "ì" => "i", "ò" => "o", "ù" => "u"
	);
	
	$string = strtr($string, $characters); 
	$string = strtolower(trim($string));
	$string = preg_replace("/[^a-z0-9-]/", "-", $string);
	$string = preg_replace("/-+/", "-", $string);
	
	if(substr($string, strlen($string) - 1, strlen($string)) === "-") {
		$string = substr($string, 0, strlen($string) - 1);
	}
	
	return $string;
}

//¨******EMPIEZA AGREGAR FABRICANTE¨******

public function agregar_fabricante($marca)
{
 			$seleccionar=mysql_query("SELECT mf_name FROM lhs6n_virtuemart_manufacturers_es_es WHERE mf_name='$marca'");
			if (mysql_num_rows($seleccionar)==0) {
			$string=$marca;
			$slug=$this->slug($string);
			$registrar="INSERT INTO lhs6n_virtuemart_manufacturers_es_es".
			"(mf_name, slug)".
			"VALUES('$marca','$slug')";
			$registro=mysql_query($registrar);
			$registrar_rel="INSERT INTO lhs6n_virtuemart_manufacturers".
			"(virtuemart_manufacturercategories_id, published, created_on, created_by, locked_on, locked_by)".
			"VALUES('1', '1', Now(), '161', '0000-00-00 00:00:00', '0')";
			$registro_rel=mysql_query($registrar_rel);
			return "Se registro en la BD: ".$marca."<br>"; }
	}
//¨******TERMINA AGREGAR FABRICANTE¨******

//¨******EMPIEZA AGREGAR CATEGORIA******
	public function agregar_categoria($grupo)
	{
		$seleccionarcat=mysql_query("SELECT category_name FROM lhs6n_virtuemart_categories_es_es WHERE category_name='$grupo'");
			if(mysql_num_rows($seleccionarcat)==0){
			$string=$grupo;
			$slug=$this->slug($string);
			$registrar_categoriarel="INSERT INTO lhs6n_virtuemart_categories_es_es".
			"(category_name, slug)".
			"VALUES('$grupo', '$slug')";
			$registro=mysql_query($registrar_categoriarel);
			if($registro)  {
			$registrar="INSERT INTO lhs6n_virtuemart_categories".
			"(virtuemart_vendor_id, category_layout, category_product_layout, products_per_row, limit_list_start, limit_list_step, hits, ordering, created_on, created_by, locked_on, locked_by)".
			" VALUES('1', 'default', 'default', '1', '0','10','0','1', Now(), '161', '0000-00-00 00:00:00','0')";
			$registro_rel=mysql_query($registrar);
						$rs = mysql_query("SELECT MAX(id) AS id FROM lhs6n_virtuemart_category_categories");
		if ($row = mysql_fetch_row($rs)) {
		$id = trim($row[0]);
		$valor=$id+1;
		}	$registrar_relacion="INSERT INTO lhs6n_virtuemart_category_categories".
			"(category_child_id, ordering)".
			"VALUES('$valor', '$slug')";
			$registro_rela=mysql_query($registrar_relacion);
			if ($registro_rel) {
				return "Grupo ya dado de alta en la BD"."<br>";
			}
			else{
				echo "Error";
			}} }
	
		 else{
	return "Categoria en la BD"."<br>";
	}
	}
//¨******TERMINA AGREGAR CATEGORIA******

//¨******EMPIEZA AGREGAR PRODUCTOS******
public function agregar_producto($clave, $disponible,$grupo, $marca, $precio, $moneda, $cambio, $descripcion, $nombre, $foto)
	{

/*TABLAS A OCUPAR:
	lhs6n_virtuemart_products x
	lhs6n_virtuemart_product_categories x
	lhs6n_virtuemart_product_manufacturers x
	lhs6n_virtuemart_products_es_es
	lhs6n_virtuemart_product_price, peso mexicano=168 X
	lhs6n_virtuemart_product_customfields EL id = 21
	Total de productos registrados: 1314*/
$seleccionar_clave=mysql_query("SELECT product_sku FROM lhs6n_virtuemart_products WHERE product_sku='$clave'");
	$parametro="min_order_level=''|max_order_level=''|product_box=''|";
	if (mysql_num_rows($seleccionar_clave)==0) {
	$insertar_producto="INSERT INTO lhs6n_virtuemart_products".
	"(virtuemart_vendor_id, product_parent_id, product_sku, product_in_stock, product_ordered, low_stock_notification, product_available_date, product_special, product_sales, product_params, hits, layout, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by)".
	"VALUES('1', '0', '$clave', '$disponible', '1', '2', Now(), '0','0','$parametro','0','0','1', Now(), '161','0000-00-00 00:00:00','0', '0000-00-00 00:00:00', '0')";
	$insertado=mysql_query($insertar_producto);
	if($insertado){//INSERTAR CATEGORIAS
	$rs = mysql_query("SELECT MAX(virtuemart_product_id) AS virtuemart_product_id FROM lhs6n_virtuemart_products");
		if ($row = mysql_fetch_row($rs)) {
		$virtuemart_product_id = trim($row[0]);
				 }
		$select_marca=mysql_query("SELECT virtuemart_category_id FROM lhs6n_virtuemart_categories_es_es WHERE category_name='$grupo'");
		if ($row = mysql_fetch_row($select_marca)) {
		$categoria = trim($row[0]); }
			$insertar_categoria="INSERT INTO lhs6n_virtuemart_product_categories".
			"(virtuemart_product_id, virtuemart_category_id, ordering)".
			"VALUES ('$virtuemart_product_id', '$categoria', '0')";
			$insertar_prod_cat=mysql_query($insertar_categoria);
				if($insertar_prod_cat){//INSERTAR FABRICANTE
							$select_fabri=mysql_query("SELECT virtuemart_manufacturer_id FROM lhs6n_virtuemart_manufacturers_es_es WHERE mf_name='$marca'");
						if ($row2 = mysql_fetch_row($select_fabri)) {
							$fabricante = trim($row2[0]); }
							$insertar_marca="INSERT INTO lhs6n_virtuemart_product_manufacturers".
							"(virtuemart_product_id, virtuemart_manufacturer_id)".
							"VALUES ('$virtuemart_product_id', '$fabricante')";
							$insertar_producto_marca=mysql_query($insertar_marca);
							$aumento=$precio * 0.21;
							if($moneda=="Dolares"){
										$precio=$aumento + $precio;
										$preciotot=$precio * $cambio;
										} else{	$preciotot=$aumento + $precio; }
									if($insertar_producto_marca){//INSERTAR PRECIOS
									$insertar_precio="INSERT INTO lhs6n_virtuemart_product_prices".
									"(virtuemart_product_id, virtuemart_shoppergroup_id, product_price, override, product_override_price, product_tax_id, product_discount_id, product_currency, product_price_publish_up, product_price_publish_down, price_quantity_start, price_quantity_end, created_on, created_by, modified_on, modified_by, locked_on, locked_by )".
									"VALUES ('$virtuemart_product_id', '0', '$preciotot','0','0.00000', '-1', '-1', '168', Now(), '0000-00-00 00:00:00','0', '0', Now(), '161', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0')";
									$insertar_producto_precio=mysql_query($insertar_precio);
										if($insertar_producto_precio){//INSERTAR NOMBRE, DESCRIPCIONES Y FOTO
											$string=$nombre;
											$slug=$this->slug($string);	
											$insertar_descrip="INSERT INTO lhs6n_virtuemart_products_es_es".
											"(product_desc, product_name, metadesc, slug)".
											"VALUES ('$descripcion', '$nombre', '$foto', '$slug')";
											$insertar_producto_nombre=mysql_query($insertar_descrip);
												if($insertar_producto_nombre){
													$insertar_personalizado="INSERT INTO lhs6n_virtuemart_product_customfields".
													"(virtuemart_product_id, virtuemart_custom_id, custom_value, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by, ordering)".
													"VALUES('$virtuemart_product_id','21', '$foto', '0', Now(), '161', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0')";
													$insertar_producto_foto=mysql_query($insertar_personalizado);
														if($insertar_producto_foto){ echo "sSe completó inserción del producto en la BD: ". $nombre. "<br>"; }
												}

										}
							}
				}
		}
	}  else { echo "El producto: ".$clave." está en la BD"; }

}

//¨******TERMINA AGREGAR PRODUCTOS******

//¨******EMPIEZA AMODIFICAR PRODUCTOS******
	public function modificar_productos($cambio, $clave, $precio, $moneda, $disponible)
 {
 	
		$seleccionar_prod=mysql_query("SELECT product_sku, virtuemart_product_id, product_in_stock FROM lhs6n_virtuemart_products WHERE product_sku='$clave'");
		if ($row2 = mysql_fetch_row($seleccionar_prod)) {
			$virtuemart_product_id = trim($row2[1]);
			$stock = trim($row2[2]);
			$seleccionar_precio=mysql_query("SELECT virtuemart_product_id, product_price FROM lhs6n_virtuemart_product_prices WHERE virtuemart_product_id='$virtuemart_product_id'");
		if ($row = mysql_fetch_row($seleccionar_precio)) {
			$price = trim($row[1]); 
				}
		if($price==$precio){
			 } else { 
			 	if($moneda=="Dolares"){
			$precio=$precio*$cambio;
		}	
			 	$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio' WHERE virtuemart_product_id='$virtuemart_product_id'"); 
	
		}
		if($stock==$disponible){ } 
		else {
		 $modificar_stock=mysql_query("UPDATE lhs6n_virtuemart_products SET product_in_stock='$disponible' WHERE virtuemart_product_id='$virtuemart_product_id'");
				 } 
		} else{ echo "Sin modificación: ".$clave; }
		}
//¨******TERMINA MODIFICAR PRODUCTOS******

//¨******EMPIEZA BAJA DE PRODUCTOS******
public function bajas_productos($clave)
	{
		$seleccionar_prod=mysql_query("SELECT product_sku, virtuemart_product_id, product_in_stock FROM lhs6n_virtuemart_products WHERE product_sku='$clave'");
		if ($row2 = mysql_fetch_row($seleccionar_prod)) {
			$virtuemart_product_id = trim($row2[1]); 
			$stock= trim($row2[2]); }
		if($stock==0){
		$borrar_clave=mysql_query("DELETE FROM lhs6n_virtuemart_products WHERE virtuemart_product_id='$$virtuemart_product_id'");
		$borrar_categoria=mysql_query("DELETE FROM lhs6n_virtuemart_categories_es_es WHERE virtuemart_product_id='$$virtuemart_product_id'");
		$borrar_fabricante=mysql_query("DELETE FROM virtuemart_manufacturer_id WHERE virtuemart_product_id='$$virtuemart_product_id'");
		$borrar_precio=mysql_query("DELETE FROM lhs6n_virtuemart_product_price WHERE virtuemart_product_id='$$virtuemart_product_id'");
		$borrar_descripcion=mysql_query("DELETE FROM lhs6n_virtuemart_products_es_es WHERE virtuemart_product_id='$$virtuemart_product_id'");
		$borrar_foto=mysql_query("DELETE FROM lhs6n_virtuemart_product_customfields WHERE virtuemart_product_id='$$virtuemart_product_id'");
		}

}
//¨******TERMINA BAJA DE PRODUCTOS******

public function agregar_promos($cambio, $clave, $precio, $promocion)
/*
3% 5%  10% 23% 7%
40% 50% 
15%
VencimientoPromocion=product_price_publish_down
precio normal será modificado multiplicado por .21 + $precio
xon descuento el precio será sin modificaciones*/
{
 	if($moneda=="Dolares"){
			$precio=$precio*$cambio;
		}
		$seleccionar_prod=mysql_query("SELECT product_sku, virtuemart_product_id, product_in_stock FROM lhs6n_virtuemart_products WHERE product_sku='$clave'");
		if ($row2 = mysql_fetch_row($seleccionar_prod)) {
			$virtuemart_product_id = trim($row2[1]);
			$stock = trim($row2[2]) ;
		}		
			$seleccionar_precio=mysql_query("SELECT virtuemart_product_id, product_price FROM lhs6n_virtuemart_product_prices WHERE virtuemart_product_id='$virtuemart_product_id'");
		if ($row = mysql_fetch_row($seleccionar_precio)) {
			$price = trim($row[1]); 
				}
				$aumento=$precio*.21;
				$precio=$precio - $aumento;
			switch ($promocion) {
				case '5%':
					 $modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='-1' WHERE virtuemart_product_id='$virtuemart_product_id'"); 
					break;
				case '3%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='6' WHERE virtuemart_product_id='$virtuemart_product_id'"); 				
					break;
				case '7%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='8' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				case '10%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='9' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				case '15%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='10' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				case '23%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='11' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				case '40%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='12' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				case '50%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='13' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				case '8%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='14' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				case '20%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='15' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				case '30%':
					$modificar_precio=mysql_query("UPDATE lhs6n_virtuemart_product_prices SET product_price='$precio', product_tax_id='1', product_discount_id='16' WHERE virtuemart_product_id='$virtuemart_product_id'"); 					
					break;
				default:
				echo "No Sirve Script". "<br>";
					
					
			}
		}
}
			


$mysqlconexion= New Conexion();
$mysqlconexion->hostdb($host, $base);
$mysqlconexion->usuario($usuario, $pass);
$mysqlconexion->con('si');
$xml=simplexml_load_file("http://www.grupocva.com/catalogo_clientes_xml/lista_precios.xml?cliente=26813&marca=%&grupo=%&clave=%&codigo=%&promos=1&tc=1&dc=1&dt=1");
if($xml){
			$i=0;
			foreach ($xml->item as $child) {
			//LEYENDO EL XML
			
			$grupo=$child->grupo;
			$marca= $child->marca;
			$clave=$child->clave;
			$disponible=$child->disponible;
			$precio=$child->precio;
			$moneda=$child->moneda;
			$cambio=$child->tipocambio;
			$nombre=$child->descripcion;
			$descripcion=$child->ficha_comercial;
			$foto=$child->imagen;
			$promocion=$child->TotalDescuento;
			$mysqlconexion->agregar_categoria($grupo);
			echo $mysqlconexion->agregar_fabricante($marca);
			/*if($promocion=="Sin Descuento"){
			 } else { echo $promocion.": ". $clave."<br>"; }*/
			 if($disponible>1){
			 //	$mysqlconexion->agregar_promos($cambio, $clave, $precio, $promocion);
			// $mysqlconexion->modificar_productos($cambio, $clave, $precio, $moneda, $disponible);			
			$mysqlconexion->agregar_producto($clave, $disponible,$grupo, $marca, $precio, $moneda, $cambio, $descripcion, $nombre, $foto);
			$i++; }
			
						}			} else { echo "¡Web Service Caido!"; }
		
	

			
			

			echo "Se registraron: ".$i."categorias en total";
				
mysql_close($mysqlconexion->descon());
//} 
	?>