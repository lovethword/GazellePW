<?

class LocalImage implements ImageStorage {
    private $Dir = SERVER_ROOT . '/public/image';
    public function __construct() {
        if (!is_dir($this->Dir)) {
            mkdir($this->Dir, 0755, true);
        }
    }
    public function upload($Name, $Content) {
        $Dir = $this->Dir . '/' . dirname($Name);
        if (!is_dir($Dir)) {
            mkdir($Dir, 0755, true);
        }
        $file = fopen($this->Dir . '/' . $Name, 'w');
        fwrite($file, $Content);
        fclose($file);
        return IMAGE_URL . '/image/' . $Name;
    }
}
