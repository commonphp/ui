# Custom Renderer

Custom renderers implement `RendererInterface`. Extending `AbstractRenderer` gives you common normalization helpers.

```php
use CommonPHP\UI\Contracts\AbstractRenderer;
use CommonPHP\UI\Contracts\ComponentInterface;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewData;

final class ArrayRenderer extends AbstractRenderer
{
    public function render(View $view): string
    {
        return $this->renderTemplate(
            $view->template(),
            $view->data(),
        );
    }

    public function renderTemplate(TemplateInterface|string $template, array|ViewData $data = []): string
    {
        $template = $this->template($template);
        $payload = $this->mergedData($template->data(), $data);

        return json_encode([
            'template' => $template->name(),
            'data' => $payload->all(),
        ], JSON_THROW_ON_ERROR);
    }

    public function renderComponent(ComponentInterface|string $component, array|ViewData $data = []): string
    {
        $component = $this->component($component);

        return $this->renderTemplate($component, $data);
    }
}
```

Use it directly:

```php
$ui = new ViewFactory(new ArrayRenderer());
```

Or through runtime driver integration:

```php
$ui = new ViewFactory();
$ui->setDriver(ArrayRenderer::class);
```
