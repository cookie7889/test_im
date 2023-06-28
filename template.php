<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var customOrderComponent $component */
\Bitrix\Main\UI\Extension::load("ui.vue3");
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs($templateFolder.'/jquery-3.7.0.slim.min.js');
Asset::getInstance()->addJs($templateFolder.'/ajax.js');
?>

<section class="basket" id="fullBasket">
	<div class="basket__prev-button">
		<a href="/catalog/">Назад в каталог</a>
	</div>
	<div class="basket__title-line">
		<div class="section__title">
			<?= $APPLICATION->ShowTitle(false); ?>
		</div>
		<div class="basket__func-buttons">
			<div class="basket__func-button" id="basketDownload">
				<span>Скачать</span>
			</div>
			<div class="basket__func-button" id="basketPrint">
				<span>Распечатать</span>
			</div>
			<div class="basket__func-button" id="basketShare">
				<span>Поделиться</span>
			</div>
			<div class="basket__func-button" id="basketClear">
				<span>Очистить</span>
			</div>
		</div>
	</div>
	<div class="basket__wrapper">
		<div class="basket__body">
			<div class="basket__quick-add">
				<div class="basket__quick-add_button">
					<span>Быстрое добавление товаров</span>
				</div>
			</div>
		<div v-if="total.ALL_COUNT > 0" class="basket__items-wrapper">
				<div class="basket__select-all" :class="allSelectedStatus" id="basketSelectAll">
					<div class="checkbox"></div>
					<span>Выбрать все</span>
				</div>
				<div class="basket__items">
					<div v-for="item in items" class="basket__item" :id="item.ID" :data-product-id="item.PRODUCT_ID">
						<div class="basket__item-select checkbox" :class="{_active: item.DELAY === 'N'}"></div>

						<div class="basket__item-img">
							<img :src="item.PICTURE" alt="">
						</div>

						<div class="basket__item-name">
							<div class="basket__item-title">
								<a :href="item.URL" target="_blank">{{item.NAME}}</a>
							</div>
							<div v-if="item.ARTICLE" class="basket__item-article">
								Артикул: {{item.ARTICLE}}
							</div>
						</div>
						<div class="basket__item-price">
							<span class="basket__item-price_current">{{item.FORMATTED_PRICE}}/{{item.MEASURE_NAME}}</span>
						</div>
						<div class="basket__item-mult"></div>
						<div class="basket__item-count">
							<div class="basket__item-count__input">
								<div class="product__quantity">
									<button class="product__quantity-button _reduce"><span class="icon"><svg>
												<use href="<?= SITE_TEMPLATE_PATH ?>/assets/svg/sprites.svg#minus_icon"></use>
											</svg></span></button>
									<input type="number" :value="item.QUANTITY" min="1" max="14">
									<button class="product__quantity-button _add"><span class="icon"><svg>
												<use href="<?= SITE_TEMPLATE_PATH ?>/assets/svg/sprites.svg#plus_icon"></use>
											</svg></span></button>
								</div>
							</div>
							<div class="basket__item-count__measure">
								{{item.MEASURE_NAME}}
							</div>
						</div>
						<div class="basket__item-equals"></div>
						<div class="basket__item-total">
							<div class="product__price">
								{{item.FORMATTED_TOTAL_PRICE}}
							</div>
						</div>
						<div class="basket__item-buttons">
							<div class="basket__item-favorite" id="addToFavorite">
								<svg>
									<use href="<?= SITE_TEMPLATE_PATH ?>/assets/svg/sprites.svg#favorite__icon"></use>
								</svg>
							</div>
							<div class="basket__item-drope">
								<svg>
									<use href="<?= SITE_TEMPLATE_PATH ?>/assets/svg/sprites.svg#drope_icon"></use>
								</svg>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-if="total.COUNT > 0" class="basket__result">
			<div class="basket__result-wrapper">
				<div class="basket__result-title">
					Ваш заказ
				</div>
				<div class="basket__result-items">
					<div class="basket__result-item">
						<div class="basket__result-item__name">{{total.FORMATTED_COUNT}}:</div>
						<div v-if="total.DISCOUNT > 0" class="basket__result-item__value">{{total.FORMATTED_PRICE_WITHOUT_DISCOUNT}}</div>
						<div v-else class="basket__result-item__value">{{total.FORMATTED_PRICE}}</div>
					</div>
					<div v-if="total.DISCOUNT > 0" class="basket__result-item">
						<div class="basket__result-item__name">Скидка по акциям:</div>
						<div class="basket__result-item__value _discount">{{total.FORMATTED_DISCOUNT}}</div>
					</div>
					<div v-if="total.DISCOUNT > 0" class="basket__result-item">
						<div class="basket__result-item__name">Общая стоимость:</div>
						<div class="basket__result-item__value _total">{{total.FORMATTED_PRICE}}</div>
					</div>
				</div>
				<div class="basket__result-button" id="goToOrder">
					<span>Оформить заказ</span>
				</div>
			</div>
		</div>
	</div>
	<div v-if="total.ALL_COUNT == 0" class="basket__empty">
		Корзина пуста. Добавьте товары в корзину для оформления заказа.
	</div>
	<?/*
	<div class="vue">
		totalCount: {{total.COUNT}}<br>
		totalFormattedCount: {{total.FORMATTED_COUNT}}<br>
		items:<br>
		<ul>
        	<li v-for="item in items">
				{{item.PICTURE}}
        	</li>
    	</ul>
	</div>
	*/?>
</section>

<script type="text/javascript">
	const basketApp = BX.Vue3.BitrixVue.createApp({
		data()
		{
			return {
				total: <?=json_encode($arResult['TOTAL'])?>,
				items: <?=json_encode($arResult['ITEMS'])?>,
				allSelectedStatus: 'all' 
			}
		},
		methods:
		{
			checkSelected(selector) {
				let items = document.querySelectorAll(selector);
				let activeCount = 0;
				for (let item of items) {
					if (item.classList.contains('_active')) {
						activeCount++;
					}
				}
				
				if (activeCount === 0) {
					return 'none';
				} else if (activeCount === items.length) {
					return 'all';
				} else {
					return 'some';
				}
			},
			updateData() {
				const params = new URLSearchParams();
				params.append('action', 'updateData');
				fetch('<?=$templateFolder?>/ajax.php', {
					method: 'POST',
					body: params
				})
				.then(response => response.json())
				.then(data => {
					this.total = data.total;
				})
				.catch(error => console.error(error));
			},
			delayItem(id, status) {
				const params = new URLSearchParams();
				params.append('action', 'delayItem');
				params.append('id', id);
				params.append('status', status);
				fetch('<?=$templateFolder?>/ajax.php', {
					method: 'POST',
					body: params
				})
				.then(response => response.text())
				.then(result => {
					if (result === 'success') {
						this.updateData();
					}
				})
				.catch(error => console.error(error));
			},
		},
		mounted()
		{
			this.updateData();

			function detectCheckbox(selector, callback) {
				const observer = new MutationObserver(mutations => {
					mutations.forEach(mutation => {
						if (mutation.type === 'attributes' && mutation.attributeName === 'class' && mutation.target.matches(selector)) {
							callback(mutation.target);
						}
					});
				});
				observer.observe(document, { attributes: true, subtree: true });
			}

			const checkboxes = document.querySelectorAll('.basket__item-select.checkbox');
			let activeCount = 0,
				allSelect = '';
			for (let checkbox of checkboxes) {				
				checkbox.addEventListener('click', event => {
					if (event.currentTarget.classList.contains('_active')) {
						event.currentTarget.classList.remove('_active');
					} else {
						event.currentTarget.classList.add('_active');
					}
					this.allSelectedStatus = this.checkSelected('.basket__item-select');
				});
			}
			detectCheckbox('.basket__item-select.checkbox', (element) => {
				const id = element.parentNode.id;
				let status = '';
				if (element.className.indexOf('_active') > -1) {
					status = 'remove';
				} else {
					status = 'add';
				} 
				this.delayItem(id, status);
			});

			this.allSelectedStatus = this.checkSelected('.basket__item-select');

			const basketSelectAll = document.getElementById('basketSelectAll');
			basketSelectAll.addEventListener('click', event => {
				if (event.currentTarget.classList.contains('all')) {
					for (let checkbox of checkboxes) {
						checkbox.classList.remove('_active');
					}
				} else if (event.currentTarget.classList.contains('none')) {
					this.allSelectedStatus = 'none';
					for (let checkbox of checkboxes) {
						checkbox.classList.add('_active');
					}
				} else if (event.currentTarget.classList.contains('some')) {
					let activeCount = 0;
					for (let checkbox of checkboxes) {
						if (checkbox.classList.contains('_active')) {
							activeCount++;
						}
					}
					if (activeCount >= Math.floor(checkboxes.length / 2)) {
						for (let checkbox of checkboxes) {
							checkbox.classList.add('_active');
						}
					} else {
						for (let checkbox of checkboxes) {
							checkbox.classList.remove('_active');
						}
					}
				}
				this.allSelectedStatus = this.checkSelected('.basket__item-select');
			});
		}
	});
	basketApp.mount('#fullBasket');
</script>

<?
echo '<pre>';
//$basket = $component->getBasket();
print_r($arResult);
echo '</pre>';
?>
