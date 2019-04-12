<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Action;

use GuzzleHttp\Client;
use Shopware\Core\Framework\Struct\Collection;
use Shopware\Core\Framework\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
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
        if (($violations = $this->validate($configuration))->count() > 0) {
            throw new ConstraintViolationException($violations, $configuration);
        }

        $options = !empty($configuration['options'])
            ? json_decode($this->stringTemplateRenderer->render(json_encode($configuration['options']), $data->getElements()), true)
            : [];

        $client = new Client();
        $client->request(
            $configuration['method'],
            $configuration['url'],
            $options
        );
    }

    private function validate(array $configuration): ConstraintViolationListInterface
    {
        return $this->validator->validate(
            $configuration,
            new Assert\Collection(
                [
                    'method' => [
                        new Assert\NotBlank(),
                    ],
                    'url' => [
                        new Assert\NotBlank(),
                        new Assert\Url(),
                    ],
                    'options' => [],
                ]
            )
        );
    }
}
