<?php declare(strict_types=1);

namespace Compolomus\Template;

/**
 * Class Template
 * Idea from https://github.com/rainbow-cat
 */
class Template
{
    private $dir;

    private $ext;

    private $functions;

    private $data = [];

    public function __construct(string $dir, string $ext, array $functions = [])
    {
        $this->dir = $dir;
        $this->ext = $ext;
        $this->functions = $functions;
    }

    public function __call(string $name, array $arguments)
    {
        $action = substr($name, 0, 3);
        $property = strtolower(substr($name, 3));
        switch ($action) {
            case 'get':
                return $this->$property ?? null;

            case 'set':
                $this->$property = $arguments[0];
                break;

            default :
                return is_callable([$this, $name])
                    ? call_user_func_array($this->functions[$name], $arguments)
                    : '<!-- <h1>Tpl debug<h1><h3>Registered functions</h3><pre>' . print_r($this->functions,
                        true) . '</pre> -->';
        }
    }

    private function getData(): void
    {
        if (count($this->data)) {
            foreach ($this->data as $key => $val) {
                $k = 'set' . $key;
                $this->$k($val);
            }
        }
    }

    public function data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function args(array $args)
    {
        return call_user_func_array(array($this, 'render'), $args);
    }

    public function render(string $tpl, $key = null)
    {
        $this->getData();

        ob_start();

        if (is_file($this->dir . DIRECTORY_SEPARATOR . $tpl . '.' . $this->ext)) {
            include $this->dir . DIRECTORY_SEPARATOR . $tpl . '.' . $this->ext;
        } else {
            echo '<!-- <h1>Tpl debug<h1><h3>Tpl file not exists<h3><pre>' . print_r($this, true) . '</pre> -->';
        }

        if ($key === null) {
            return ob_get_clean();
        }

        $this->$key = ob_get_clean();

        return $this;
    }
}
