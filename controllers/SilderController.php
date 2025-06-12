<?php
require_once 'models/Slider.php';

class SliderController {
    public function index() {
        $sliderModel = new Slider();
        $sliders = $sliderModel->getAllSliders();
        return $sliders;
    }
}
?>
