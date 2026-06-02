<?php

namespace App\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class CheckoutContainer extends Component
{

    private array $passes;
    private string $current;

    private string $classes = 'hidden lg:flex w-[30px] h-[30px] text-white text-base font-semibold rounded-full justify-center items-center';
    private string $currentClasses;
    private string $passClasses;
    private string $defaultClasses;

    public string $accountClasses;
    public string $accountContent;
    public string $accountColor;

    public string $shippingClasses;
    public string $shippingContent;
    public string $shippingColor;

    public string $paymentClasses;
    public string $paymentContent;
    public string $paymentColor;

    public string $confirmClasses;
    public string $confirmContent;
    public string $confirmColor;
    public array $productsData;
    public Collection $partners;


//    public string $quantity;
//    public string $total;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($current, $productsData = [], $partners = new Collection())
    {
        $this->current = $current;
//        $this->total = $total;
//        $this->quantity = $quantity;

        $this->passes = match ($current)
        {
            'shipping' => ['account'],
            'payment' => ['account', 'shipping'],
            'confirm' => ['account', 'shipping', 'payment', 'confirm'],
            default => []
        };
        $this->currentClasses = $this->classes . ' bg-primary-400';
        $this->passClasses = $this->classes . ' bg-success-500';
        $this->defaultClasses = 'temp-classes-default';

        $this->accountClasses = $this->getStepClass('account');
        $this->accountContent = $this->getStepContent('account');
        $this->accountColor = $this->getStepColor('account');

        $this->shippingClasses = $this->getStepClass('shipping');
        $this->shippingContent = $this->getStepContent('shipping');
        $this->shippingColor = $this->getStepColor('shipping');

        $this->paymentClasses = $this->getStepClass('payment');
        $this->paymentContent = $this->getStepContent('payment');
        $this->paymentColor = $this->getStepColor('payment');

        $this->confirmClasses = $this->getStepClass('confirm');
        $this->confirmContent = $this->getStepContent('confirm');
        $this->confirmColor = $this->getStepColor('confirm');

        $this->productsData = $productsData;

        $this->partners = $partners;

        /*$this->partnersClasses = $this->getStepClass('partners');
        $this->partnersContent = $this->getStepContent('partners');
        $this->partnersColor = $this->getStepColor('partners');*/

    }

    public function getStepContent($step)
    {
        $content = match ($step)
        {
            'account' => '1',
            'shipping' => '2',
            'payment' => '3',
            'confirm' => '4',
        };
        if (in_array($step, $this->passes))
        {
            $content = '<i data-feather="check-circle" class="w-5 h-5"></i>';
        }
        return $content;
    }

    public function getStepColor($step)
    {
        $color = $step === $this->current ? 'primary-400' : 'gray-450';
        if (in_array($step, $this->passes))
        {
            $color = 'success-500';
        }
        return $color;
    }

    function getStepClass($step)
    {
        $relatedClass = $step === $this->current ? $this->currentClasses : $this->defaultClasses;
        if (in_array($step, $this->passes))
        {
            $relatedClass = $this->passClasses;
        }
        return $relatedClass;
    }


    public function render()
    {
        return view('shop::components.checkout.partials.checkout-container');
    }
}
