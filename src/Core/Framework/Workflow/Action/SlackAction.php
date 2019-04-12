<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Action;

use GuzzleHttp\Client;
use Shopware\Core\Framework\Struct\Collection;
use Shopware\Core\Framework\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SlackAction implements ActionInterface
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
        return 'sw-slack';
    }

    public function execute(array $configuration, Collection $data): void
    {
        if (($violations = $this->validate($configuration))->count() > 0) {
            throw new ConstraintViolationException($violations, $configuration);
        }

        $text = $this->stringTemplateRenderer->render($configuration['text'], $data->getElements());

        $client = new Client();
        $client->post(
            $configuration['slackWebHook'],
            [
                'json' => [
                    'text' => $text,
                ],
            ]
        );
    }

    private function validate(array $configuration): ConstraintViolationListInterface
    {
        return $this->validator->validate(
            $configuration,
            new Assert\Collection(
                [
                    'slackWebHook' => [
                        new Assert\NotBlank(),
                    ],
                    'text' => [
                        new Assert\NotBlank(),
                    ],
                ]
            )
        );
    }
}
