<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Action;

use GuzzleHttp\Client;
use Shopware\Core\Framework\Struct\Collection;
use Shopware\Core\Framework\Twig\StringTemplateRenderer;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WebhookAction implements ActionInterface
{
    /**
     * @var StringTemplateRenderer
     */
    private $stringTemplateRenderer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(StringTemplateRenderer $stringTemplateRenderer, ValidatorInterface $validator)
    {
        $this->stringTemplateRenderer = $stringTemplateRenderer;
        $this->validator = $validator;
    }

    public function getHandlerIdentifier(): string
    {
        return 'sw-webhook';
    }

    public function execute(array $configuration, Collection $data): void
    {
        $this->validate($configuration);

        $options = !empty($configuration['options'])
            ? json_decode($this->stringTemplateRenderer->render($configuration['options'], $data->toArray()['elements']), true)
            : [];

        $client = new Client();
        $client->request(
            $configuration['method'],
            $configuration['url'],
            $options
        );
    }

    private function validate(array $configuration): void
    {
        $this->validator->validate(
            $configuration['method'],
            [
                new NotBlank(),
            ]
        );

        $this->validator->validate(
            $configuration['url'],
            [
                new NotBlank(),
                new Url(),
            ]
        );
    }
}
