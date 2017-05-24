<?php
/**
 * ==================== v1 ====================
 */
class Button
{
    private $lamp;

    public function poll()
    {
        if (true) {
            $this->lamp->turnOn();
        }
    }
}

class Lamp
{
    public function turnOn() {}
    public function turnOff() {}
}
/**
 * 后果:
 * - Button 依赖于 Lamp, 当 Lamp 改变时会影响 Button
 * - 无法重用 Button 去控制其他设备, 比如 Motor
 */

/**
 * ==================== v2 ====================
 */
interface SwitchableDevice
{
    public function turnOn();
    public function turnOff();
}

class Button2
{
    private $device;

    public function __construct(SwitchableDevice $device)
    {
        $this->device = $device;
    }

    public function poll()
    {
        if (true) {
            $this->device->turnOn();
        }
    }
}

class Lamp2 implements SwitchableDevice
{
    public function turnOn()
    {
        echo 'lamp turned on';
    }

    public function turnOff() {}
}

class Motor2 implements SwitchableDevice
{
    public function turnOn()
    {
        echo 'motor turned on';
    }

    public function turnOff() {}
}

$button = new Button2(new Motor2()); // note: 构造器依赖注入
$button->poll();