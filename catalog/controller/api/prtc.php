<?php
include_once 'dep.php';

class ControllerApiPrtc extends Controller{

    public function bs(){
        $this->load->model('admin/order');

        $time = $this->getInput('time');
        if($time == 'now') $time = time();
        else $time = (int)$time;

        $orders = $this->model_admin_order->getNewestOrders($time);

        $nots = array();

        if(count($orders) == 1){
            $nots[] = array(
                'title' => 'New order',
                'content' => 'A new order have been placed with a total of ' . $this->formatPrice($orders[0]['total'])
            );
        }else if(count($orders) > 1){
            $nots[] = array(
                'title' => 'New orders',
                'content' => 'You have ' . count($orders) . ' new orders.'
            );
        }

        $response = array(
            'orders' => count($orders),
            'time' => time() . '',
            'notifications' => $nots
        );

        $this->respond_json($response);
    }

    private function formatPrice($price){
        return number_format($price, 2, ".", "");
    }

}
