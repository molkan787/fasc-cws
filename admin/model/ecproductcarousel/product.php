<?php
class ModelEcproductcarouselProduct extends Model {
	public function checkInstall(){
		$sql = " SHOW TABLES LIKE '".DB_PREFIX."customer_view_product'";
		$query = $this->db->query( $sql );

		if( count($query->rows) <=0 )
			$this->createTables();

		return ;
	}

	public function createTables(){
		$sql = array();
		$sql[] = "
			CREATE TABLE IF NOT EXISTS `".DB_PREFIX."customer_view_product` (
			  `customer_id` int(11) NOT NULL,
			  `product_id` int(11) NOT NULL,
			  `viewed` int(11) DEFAULT '0',
			  `date_added` datetime DEFAULT NULL,
			  `date_modified` datetime DEFAULT NULL,
			  `ip` varchar(40) DEFAULT NULL,
			  `browser` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`customer_id`,`product_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";

		foreach( $sql as $q ){
				$query = $this->db->query( $q );
			}

	}
}