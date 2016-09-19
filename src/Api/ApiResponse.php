<?php //strict

namespace LayoutCore\Api;

use Illuminate\Http\Response;
use Plenty\Modules\Account\Events\FrontendUpdateCustomerSettings;
use Plenty\Modules\Authentication\Events\AfterAccountAuthentication;
use Plenty\Modules\Authentication\Events\AfterAccountContactLogout;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemRemove;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemUpdate;
use Plenty\Modules\Basket\Events\BasketItem\BeforeBasketItemAdd;
use Plenty\Modules\Basket\Events\BasketItem\BeforeBasketItemRemove;
use Plenty\Modules\Basket\Events\BasketItem\BeforeBasketItemUpdate;
use Plenty\Modules\Frontend\Events\FrontendCurrencyChanged;
use Plenty\Modules\Frontend\Events\FrontendLanguageChanged;
use Plenty\Modules\Frontend\Events\FrontendUpdateDeliveryAddress;
use Plenty\Modules\Frontend\Events\FrontendUpdatePaymentSettings;
use Plenty\Modules\Frontend\Events\FrontendUpdateShippingSettings;
use Plenty\Plugin\Application;
use Plenty\Plugin\Events\Dispatcher;

class ApiResponse
{
	/**
	 * @var Dispatcher
	 */
	private $dispatcher;
	/**
	 * @var array
	 */
	private $eventData = [];
	/**
	 * @var mixed
	 */
	private $data = null;

	private $notifications = [
		"error"   => null,
		"success" => null,
		"info"    => null
	];
	/**
	 * @var array
	 */
	private $headers = [];

	/**
	 * @var null|Application
	 */
	private $app = null;

	public function __construct(Dispatcher $dispatcher, Application $app)
	{
		$this->app = $app;
		$this->dispatcher = $dispatcher;

		// register Basket Item Events
		$this->dispatcher->listen(BeforeBasketItemAdd::class, function ($event)
		{
			$this->eventData["BeforeBasketItemAdd"] = [
				"basketItem" => $event->getBasketItem()
			];
		});
		$this->dispatcher->listen(AfterBasketItemAdd::class, function ($event)
		{
			$this->eventData["AfterBasketItemAdd"] = [
				"basketItem" => $event->getBasketItem()
			];
		});
		$this->dispatcher->listen(BeforeBasketItemRemove::class, function ()
		{
			$this->eventData["BeforeBasketItemRemove"] = [];
		});
		$this->dispatcher->listen(AfterBasketItemRemove::class, function ()
		{
			$this->eventData["AfterBasketItemRemove"] = [];
		});
		$this->dispatcher->listen(BeforeBasketItemUpdate::class, function ()
		{
			$this->eventData["BeforeBasketItemUpdate"] = [];
		});
		$this->dispatcher->listen(AfterBasketItemUpdate::class, function ()
		{
			$this->eventData["AfterBasketItemUpdate"] = [];
		});

		// register Frontend Events
		$this->dispatcher->listen(FrontendCurrencyChanged::class, function ($event)
		{
			$this->eventData["FrontendCurrencyChanged"] = [
				"curency"       => $event->getCurrency(),
				"exchangeRatio" => $event->getCurrencyExchangeRatio()
			];
		});
		$this->dispatcher->listen(FrontendLanguageChanged::class, function ($event)
		{
			$this->eventData["FrontendLanguageChanged"] = [
				"language" => $event->getLanguage()
			];
		});
		$this->dispatcher->listen(FrontendUpdateDeliveryAddress::class, function ($event)
		{
			$this->eventData["FrontendUpdateDeliveryAddress"] = [
				"accountAddressId" => $event->getAccountAddressId()
			];
		});
		$this->dispatcher->listen(FrontendUpdateShippingSettings::class, function ($event)
		{
			$this->eventData["FrontendUpdateShippingSettings"] = [
				"shippingCosts"         => $event->getShippingCosts(),
				"parcelServiceId"       => $event->getParcelServiceId(),
				"parcelServicePresetId" => $event->getParcelServicePresetId()
			];
		});
		$this->dispatcher->listen(FrontendUpdateCustomerSettings::class, function ($event)
		{
			$this->eventData["FrontendUpdateCustomerSettings"] = [
				"deliveryCountryId"      => $event->getDeliveryCountryId(),
				"showNetPrice"           => $event->getShowNetPrice(),
				"ebaySellerAccount"      => $event->getEbaySellerAccount(),
				"accountContactSign"     => $event->getAccountContactSign(),
				"accountContactClassId"  => $event->getAccountContactClassId(),
				"salesAgent"             => $event->getSalesAgent(),
				"accountContractClassId" => $event->getAccountContractClassId()
			];
		});
		$this->dispatcher->listen(FrontendUpdatePaymentSettings::class, function ($event)
		{
			$this->eventData["FrontendUpdatePaymentSettings"] = [
				"paymentMethodId" => $event->getPaymentMethodId()
			];
		});

		// register Auth Events
		$this->dispatcher->listen(AfterAccountAuthentication::class, function ($event)
		{
			$this->eventData["AfterAccountAuthentication"] = [
				"isSuccess"      => $event->isSuccessful(),
				"accountContact" => $event->getAccountContact()
			];
		});
		$this->dispatcher->listen(AfterAccountContactLogout::class, function ()
		{
			$this->eventData["AfterAccountContactLogout"] = [];
		});
	}

	public function error(int $code, $message = null):ApiResponse
	{
		$this->pushNotification("error", $code, $message);
		return $this;
	}

	public function success(int $code, $message = null):ApiResponse
	{
		$this->pushNotification("success", $code, $message);
		return $this;
	}

	public function info(int $code, $message = null):ApiResponse
	{
		$this->pushNotification("info", $code, $message);
		return $this;
	}

	private function pushNotification(string $context, int $code, $message = null):ApiResponse
	{
		if($message === null)
		{
			// TODO: get error message from system config
			$message = "";
		}

		$notification = [
			"code"       => $code,
			"message"    => $message,
			"stackTrace" => []
		];

		$head = $this->notifications[$context];
		if($head !== null)
		{
			$notification["stackTrace"] = $head["stackTrace"];
			$head["stackTrace"]         = [];
			array_push($notification["stackTrace"], $head);
		}

		$this->notifications[$context] = $notification;
		return $this;
	}

	public function header(string $key, string $value):ApiResponse
	{
		$this->headers[$key] = $value;
		return $this;
	}

	public function create($data, int $code = ResponseCode::OK, array $headers = []):Response
	{
		foreach($headers as $key => $value)
		{
			$this->header($key, $value);
		}

		$responseData = [];
		if($this->notifications["error"] !== null)
		{
			$responseData["error"] = $this->notifications["error"];
		}

		if($this->notifications["success"] !== null)
		{
			$responseData["success"] = $this->notifications["success"];
		}

		if($this->notifications["info"] !== null)
		{
			$responseData["info"] = $this->notifications["info"];
		}

		$responseData["events"] = $this->eventData;
		$responseData["data"]   = $data;

		return $this->app->make(Response::class, [$responseData, $code, $this->headers]);
	}
}
