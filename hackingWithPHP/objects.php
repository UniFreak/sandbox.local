<?php
require '../helper.func.php';

// called when $class class can't be found when trying to `new`
function __autoload($class) {
    if (include 'includes/'. $class . '.php') {
        eVar($class . ' file has been loaded');
    } else {
        eVar($class .' file can\'t be found');
    }
}

/**
 * Interface is a contract
 */
interface areLiving {
    function canDie();
}

class Person implements areLiving {
    public $id;
    public $name = 'unknown';
    public $gender = 'unknown';
    public $home = 'unknown';
    public $pets = [];
    // static attribute's value are same to all object
    public static $cloneId = 0;
    private $isGay = 'yes';

    // constructor
    public function __construct($name = '', $gender = '', $home = '') {
        $this->id = self::$cloneId;
        $this->name = $name;
        $this->gender = $gender;
        $this->home = $home;
    }

    // destructor
    public function __destruct() {
        eVar('sigh, I will be gone...');
    }

    // type hint
    public function teasePet(Pet $aPet) {
        $aPet->angry();
    }

    public function canDie() {
        return true;
    }

    // static method belong to class, not object
    public static function higherMe() {
        eVar('I\'m not me, I\'m the higher me, if you know what I mean');
    }

    // called when being `clone`-ed, must be public
    public function __clone() {
        eVar('strange, I\'m being cloned!');
        self::$cloneId++;
    }

    /**
     * called when being `serialize()`-ed
     *
     * note: must return a array containing all attributes that needed to be serialized
     * otherwise a notice will arise
     * and when wakeup, only those attributes in that array are recovered
     */
    public function __sleep() {
        // get_object_vars() do as it says
        foreach (get_object_vars($this) as $attr => $val) {
            eVar('logging ' . $attr . ':');
            dVar($val);
        }
        eVar('zzzzZZZZZZ');

        return ['name', 'gender'];
    }

    // called when being `unserialize()`-ed
    public function __wakeup() {
        eVar('hello again!');
    }

    // called when trying to read a non-exist class attribute
    public function __get($attrName) {
        eVar('NO, I don\'t have ' . $attrName);
    }

    // called when trying to set a non-exist class attribute
    public function __set($name, $val) {
        eVar('Oh, thank you, but I can\'t have ' . $name . ' as ' . $val . ' yet');
    }

    // called when trying to call a non-exist call method
    public function __call($method, $args) {
        eVar('SRY, can\'t do ' . $method);
    }

    // called when you trying to `echo` or `print` this object
    // must return a string value
    public function __toString() {
        eVar('Yes, yes, If you wanna store me');
        return '';
    }
}

Person::higherMe();
dVar(class_exists('Person'));

/**
 * you can loop through a object, but only public attribute will be output
 * if you wanna loop through all attribute, define a loop function inside the class
 */
eVar('==================== Looping ====================');
foreach (new Person as $attr => $val) {
    eVar($attr . ' is :');
    dVar($val);
}

/**
 * to checkout whether a variable is a object of specific class, use `instanceof`
 * to checkout whether a class/object inherited from a specific class, use is_subclass_of()
 */
eVar('==================== instance and is_subclss_of() ====================');
$john = new Person('John', 'male', 'U.S.A');
eVar('john is a ' . get_class($john));
eVar('there are other thing such as:');
dVar(get_declared_classes());
dVar(get_declared_interfaces());
eVar($john instanceof Person);
eVar((int)is_subclass_of($john, 'Person'));

/**
 * object are passed by reference, use `clone` to change this defualt behavoir
 */
eVar('==================== cloning ====================');
$anotherJohn = clone $john;
$yetAnotherJohn = new Person('john');
dVar($john);
dVar($anotherJohn);
dVar($yetAnotherJohn);

eVar('==================== compareing ====================');
$alsoJohn = $john;
eVar('$john==$anotherJohn:' . (int)($john == $anotherJohn));
eVar('$john===$anotherJohn:' . (int)($john === $anotherJohn));

/**
 * Serialization allow a object fully working cross pages
 */
eVar('==================== serialization ====================');
dVar($stonedJohn = serialize($john));
dVar(unserialize($stonedJohn));

eVar($john->penis); // triggering __get()
$john->penis = '13inch'; // triggering __set()
$john->angry(); // triggering __call()
echo $john; // triggering __toString()

$nono = new Dummy();