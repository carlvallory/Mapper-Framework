<?php
/*SCart - Session Cart*/
class SCart {

    private $_CartItems = array();

    /*Agrega items al carrito*/
	public function AddItem($id, $name, $price, $quantity, $stock=0, $discount, $discount_type, $discount_ammount, $courtesy, $expires, $event=NULL) {
        $cart = new self();

        $cart->_CartItems = $_SESSION["scart"];

        $n = 0;

        if (count($cart->_CartItems) > 0) {

            foreach ($cart->_CartItems as $Item) {

                $exists = $Item['id'] == $id ? true : false;

                if ($exists) {
                    $x = $n;
                }

                $n++;
            }
			 /*actualiza marca de tiempo para todos los items*/
			 foreach ($cart->_CartItems as $ik => $iv) {
				 $cart->_CartItems[$ik]["expires"] = $expires;
            }
        }


        if (isset($x)) {

            $ActualQuantity = $cart->_CartItems[$x]['quantity'];
            $cart->_CartItems[$x] = array(
                "id" => $id,
                "name" => $name,
                "price" => $price,
                "quantity" => $ActualQuantity + 1,
                "stock" => $stock,
				"discount_id" => $discount,
				"discount_type" => $discount_type,
				"discount_ammount" => $discount_ammount,
				"courtesy" => $courtesy,
				"expires" => $expires,
				"event" => $event
            );
			
        } else {

            $cart->_CartItems[] = array(
				"id" => $id,
				"name" => $name,
				"price" => $price,
				"quantity" => $quantity,
				"stock" => $stock,
				"discount_id" => $discount,
				"discount_type" => $discount_type,
				"discount_ammount" => $discount_ammount,
				"courtesy" => $courtesy,
				"expires" => $expires,
				"event" => $event
			);
			
        }

        $_SESSION["scart"] = $cart->_CartItems;
		
    }
	
    /*Lista items en el carrito*/
    public function GetCartItems() {
		
        $cart = new self();
		$cart->_CartItems = $_SESSION["scart"];
		$ActualItems = $cart->_CartItems;
		$cart->_CartItems = array();

		if(count($ActualItems) > 0):
		
			foreach($ActualItems as $item):
				if($item['expires'] > time()):
					$cart->_CartItems[] = array(
					"id" => $item['id'],
					"name" => $item['name'],
					"price" => $item['price'],
					"quantity" => $item['quantity'],
					"stock" => $item['stock'],
					"discount_id" => $item['discount_id'],
					"discount_type" => $item['discount_type'],
					"discount_ammount" => $item['discount_ammount'],
					"courtesy" => $item['courtesy'],
					"expires" => $item['expires'],
					"event" => $item['event']
				);
				else:
					Tickets::release($item['id']);	
				endif;
			endforeach;
			
		endif;

		$_SESSION['scart'] = $cart->_CartItems;
		
		return $cart->_CartItems;
		
    }

    /*Borra item del carrito*/
    public function DeleteItem($id){
		
        $cart = new self();
        $cart->_CartItems = $_SESSION["scart"];

        $ActualItems = $cart->_CartItems;
        $cart->_CartItems = array();


        foreach ($ActualItems as $Item) {
          
            if ($id != $Item['id']) {
                $cart->_CartItems[] = array(
					"id" => $Item['id'],
					"name" => $Item['name'],
					"price" => $Item['price'],
					"quantity" => $Item['quantity'],
					"stock" => $Item['stock'],
					"discount_id" => $Item['discount_id'],
					"discount_type" => $Item['discount_type'],
					"discount_ammount" => $Item['discount_ammount'],
					"courtesy" => $Item['courtesy'],
					"expires" => $Item['expires'],
					"event" => $Item['event']
				);
            }
        }

        $_SESSION['scart'] = $cart->_CartItems;
		
    }

    /*devuelve el total de productos en el carrito*/
    public function TotalItems() {
        $cart = new self();
        $cart->_CartItems = $_SESSION["scart"];
		
		if(count($cart->_CartItems) > 0):
			foreach ($cart->_CartItems as $Item) {
				$Total += $Item['quantity'];
			}
		else:
			$Total = 0;
		endif;

        return $Total;
    }

    /*vacia el carrito*/
    public function EmptyCart($release=true) {
        $cart = new self();
		$ActualItems = $_SESSION['scart'];
		
		/*libera los tickets*/
		if(count($ActualItems) > 0):
			foreach($ActualItems as $item):
				if($release):
					Tickets::release($item['id']);
				endif;
			endforeach;
		endif;
		
        unset($_SESSION["scart"], $cart->_CartItems);
        $cart->_CartItems = array();
    }

    /*modidica cantidad de items en el carrito*/
    public function UpdateQuantity($id, $qty) {
        
        $cart = new self();
        $cart->_CartItems = $_SESSION["scart"];
        $ActualItems = $cart->_CartItems;
        
        $cart->_CartItems = array();

        foreach ($ActualItems as $Item) {

            $quantity = $id == $Item['id'] ? $qty : $Item['quantity'];

            if ($quantity > 0) {
                $cart->_CartItems[] = array(
				"id" => $Item['id'],
				"name" => $Item['name'],
				"price" => $Item['price'],
				"quantity" => $quantity,
				"expires" => $Item['expires']
				);
            }
            $Total = $Total + $quantity;
        }
        if ($Total > 0) {
            $_SESSION["scart"] = $cart->_CartItems;
        }
    }

}

?>