import template from './sw-product-detail-context-prices.html.twig';
import './sw-product-detail-context-prices.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapState, mapGetters } = Shopware.Component.getComponentHelper();

Component.register('sw-product-detail-context-prices', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            rules: [],
            totalRules: 0,
            displayListPrice: {}
        };
    },

    computed: {
        ...mapState('swProductDetail', [
            'repositoryFactory',
            'product',
            'taxes',
            'currencies'
        ]),

        ...mapGetters('swProductDetail', [
            'isLoading',
            'defaultCurrency',
            'defaultPrice',
            'productTaxRate'
        ]),

        priceRepository() {
            if (this.product && this.product.prices) {
                return this.repositoryFactory.create(
                    this.product.prices.entity,
                    this.product.prices.source
                );
            }
            return null;
        },

        ruleRepository() {
            return this.repositoryFactory.create('rule');
        },

        priceRuleGroups() {
            const priceRuleGroups = {};

            if (!this.product.prices) {
                return priceRuleGroups;
            }

            if (!this.rules) {
                return priceRuleGroups;
            }

            const sortedPrices = this.product.prices.sort((a, b) => {
                const aRule = this.findRuleById(a.ruleId);
                const bRule = this.findRuleById(b.ruleId);

                if (!aRule || !aRule.name || !bRule || !bRule.name) {
                    return 0;
                }

                return aRule.name > bRule.name ? 1 : -1;
            });

            sortedPrices.forEach((rule) => {
                if (!priceRuleGroups[rule.ruleId]) {
                    priceRuleGroups[rule.ruleId] = {
                        ruleId: rule.ruleId,
                        rule: this.findRuleById(rule.ruleId),
                        prices: this.findPricesByRuleId(rule.ruleId)
                    };
                }
            });

            // Sort prices
            Object.values(priceRuleGroups).forEach((priceRule) => {
                priceRule.prices.sort((a, b) => {
                    return a.quantityStart - b.quantityStart;
                });
            });

            return priceRuleGroups;
        },

        priceRuleGroupsExists() {
            return Object.values(this.priceRuleGroups).length > 0;
        },

        canAddPriceRule() {
            const usedRules = Object.keys(this.priceRuleGroups).length;
            const availableRules = this.rules.length;

            return usedRules !== availableRules;
        },

        emptyPriceRuleExists() {
            return typeof this.priceRuleGroups.null !== 'undefined';
        },

        isLoaded() {
            return !this.isLoading &&
                   this.currencies &&
                   this.taxes &&
                   this.product;
        },

        currencyColumns() {
            this.sortCurrencies();

            return this.currencies.map((currency) => {
                return {
                    property: `price-${currency.isoCode}`,
                    label: currency.translated.name || currency.name,
                    visible: true,
                    allowResize: true,
                    primary: false,
                    rawData: false,
                    width: '250px'
                };
            });
        },

        pricesColumns() {
            return [
                {
                    property: 'quantityStart',
                    label: 'sw-product.advancedPrices.columnFrom',
                    visible: true,
                    allowResize: true,
                    primary: true,
                    rawData: false,
                    width: '95px'
                }, {
                    property: 'quantityEnd',
                    label: 'sw-product.advancedPrices.columnTo',
                    visible: true,
                    allowResize: true,
                    primary: true,
                    rawData: false,
                    width: '95px'
                },
                ...this.currencyColumns
            ];
        }
    },

    mounted() {
        this.mountedComponent();
    },

    methods: {
        mountedComponent() {
            const ruleCriteria = new Criteria(1, 500);
            ruleCriteria.addFilter(
                Criteria.multi('OR', [
                    Criteria.contains('rule.moduleTypes.types', 'price'),
                    Criteria.equals('rule.moduleTypes', null)
                ])
            );

            Shopware.State.commit('swProductDetail/setLoading', ['rules', true]);
            this.ruleRepository.search(ruleCriteria, Shopware.Context.api).then((res) => {
                this.rules = res;
                this.totalRules = res.total;

                Shopware.State.commit('swProductDetail/setLoading', ['rules', false]);
            });
        },

        sortCurrencies() {
            this.currencies.sort((a, b) => {
                if (a.isSystemDefault) {
                    return -1;
                }
                if (b.isSystemDefault) {
                    return 1;
                }
                if (a.translated.name < b.translated.name) {
                    return -1;
                }
                if (a.translated.name > b.translated.name) {
                    return 1;
                }
                return 0;
            });
        },

        onRuleChange(value, ruleId) {
            this.product.prices.forEach((priceRule) => {
                if (priceRule.ruleId === ruleId) {
                    priceRule.ruleId = value;
                }
            });
        },

        onAddNewPriceGroup(ruleId = null) {
            if (this.emptyPriceRuleExists) {
                return;
            }

            const newPriceRule = this.priceRepository.create(Shopware.Context.api);

            newPriceRule.ruleId = ruleId;
            newPriceRule.productId = this.product.id;
            newPriceRule.quantityStart = 1;
            newPriceRule.quantityEnd = null;
            newPriceRule.currencyId = this.defaultCurrency.id;
            newPriceRule.price = [{
                currencyId: this.defaultCurrency.id,
                gross: this.defaultPrice.gross,
                linked: this.defaultPrice.linked,
                net: this.defaultPrice.net
            }];

            this.product.prices.add(newPriceRule);

            this.$nextTick(() => {
                const scrollableArea = this.$parent.$el.children.item(0);

                if (scrollableArea) {
                    scrollableArea.scrollTo({
                        top: scrollableArea.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });
        },

        onPriceGroupDelete(ruleId) {
            const allPriceRules = this.product.prices.map(priceRule => {
                return { id: priceRule.id, ruleId: priceRule.ruleId };
            });

            allPriceRules.forEach((priceRule) => {
                if (ruleId !== priceRule.ruleId) {
                    return;
                }

                this.product.prices.remove(priceRule.id);
            });
        },

        onPriceGroupDuplicate(priceGroup) {
            if (typeof this.priceRuleGroups.null !== 'undefined') {
                return;
            }

            // duplicate each price rule
            priceGroup.prices.forEach((price) => {
                this.duplicatePriceRule(price, null);
            });
        },

        onPriceRuleDelete(priceRule) {
            // get the priceRuleGroup for the priceRule
            const matchingPriceRuleGroup = this.priceRuleGroups[priceRule.ruleId];

            // if it is the only item in the priceRuleGroup
            if (matchingPriceRuleGroup.prices.length <= 1) {
                this.createNotificationError({
                    title: this.$tc('sw-product.advancedPrices.deletionNotPossibleTitle'),
                    message: this.$tc('sw-product.advancedPrices.deletionNotPossibleMessage')
                });

                return;
            }

            // get actual rule index
            const actualRuleIndex = matchingPriceRuleGroup.prices.indexOf(priceRule);

            // if it is the last item
            if (typeof priceRule.quantityEnd === 'undefined' || priceRule.quantityEnd === null) {
                // get previous rule
                const previousRule = matchingPriceRuleGroup.prices[actualRuleIndex - 1];

                // set the quantityEnd from the previous rule to null
                previousRule.quantityEnd = null;
            } else {
                // get next rule
                const nextRule = matchingPriceRuleGroup.prices[actualRuleIndex + 1];

                // set the quantityStart from the next rule to the quantityStart from the actual rule
                nextRule.quantityStart = priceRule.quantityStart;
            }

            // delete rule
            this.product.prices.remove(priceRule.id);
        },

        onInheritanceRestore(rule, currency) {
            // remove price from rule.price with the currency id
            const indexOfPrice = rule.price.findIndex((price) => price.currencyId === currency.id);
            this.$delete(rule.price, indexOfPrice);
        },

        onInheritanceRemove(rule, currency) {
            // create new price based on the default price
            const defaultPrice = this.findDefaultPriceOfRule(rule);
            const newPrice = {
                currencyId: currency.id,
                gross: this.convertPrice(defaultPrice.gross, currency),
                linked: defaultPrice.linked,
                net: this.convertPrice(defaultPrice.net, currency)
            };

            // add price to rule.price
            this.$set(rule.price, rule.price.length, newPrice);
        },

        isPriceFieldInherited(rule, currency) {
            return rule.price.findIndex((price) => price.currencyId === currency.id) < 0;
        },

        convertPrice(value, currency) {
            const calculatedPrice = value * currency.factor;
            const priceRounded = calculatedPrice.toFixed(currency.decimalPrecision);
            return Number(priceRounded);
        },

        findRuleById(ruleId) {
            return this.rules.find((rule) => {
                return rule.id === ruleId;
            });
        },

        findPricesByRuleId(ruleId) {
            return this.product.prices.filter((item) => {
                return item.ruleId === ruleId;
            });
        },

        findDefaultPriceOfRule(rule) {
            return rule.price.find((price) => price.currencyId === this.defaultCurrency.id);
        },

        onQuantityEndChange(price, priceGroup) {
            // when not last price
            if (priceGroup.prices.indexOf(price) + 1 !== priceGroup.prices.length) {
                return;
            }

            this.createPriceRule(priceGroup);
        },

        createPriceRule(priceGroup) {
            // create new price rule
            const newPriceRule = this.priceRepository.create(Shopware.Context.api);
            newPriceRule.productId = this.product.id;
            newPriceRule.ruleId = priceGroup.ruleId;

            const highestEndValue = Math.max(...priceGroup.prices.map((price) => price.quantityEnd));
            newPriceRule.quantityStart = highestEndValue + 1;

            const price = {
                currencyId: this.defaultCurrency.id,
                gross: this.defaultPrice.gross,
                linked: this.defaultPrice.linked,
                net: this.defaultPrice.net
            };
            if (priceGroup.prices.first().price[0].listPrice) {
                price.listPrice = priceGroup.prices.first().price[0].listPrice;
            }

            newPriceRule.price = [price];
            this.product.prices.add(newPriceRule);
        },

        canCreatePriceRule(priceGroup) {
            const emptyPrices = priceGroup.prices.filter((price) => {
                return !price.quantityEnd;
            });

            return !!emptyPrices.length;
        },

        duplicatePriceRule(referencePrice, ruleId = null) {
            const newPriceRule = this.priceRepository.create(Shopware.Context.api);

            newPriceRule.productId = referencePrice.productId;
            newPriceRule.quantityEnd = referencePrice.quantityEnd;
            newPriceRule.quantityStart = referencePrice.quantityStart;
            newPriceRule.ruleId = ruleId;

            // add prices
            newPriceRule.price = [];

            referencePrice.price.forEach((price, index) => {
                this.$set(newPriceRule.price, index, { ...price });
            });

            this.product.prices.add(newPriceRule);
        },

        getPriceRuleGroupClass(number) {
            return [
                `context-price-group-${number}`
            ];
        },

        onListPriceOpen(ruleId) {
            this.$set(this.displayListPrice, ruleId, true);
        },

        getDefaultPrice(ruleId) {
            return this.findPricesByRuleId(ruleId).find((price) => price.price[0].currencyId === this.defaultCurrency.id)
                .price[0];
        },

        getPricesForListPrice(prices) {
            return prices.first().price;
        },

        getCurrency(currencyId) {
            return this.currencies.find((currency) => currency.id === currencyId);
        },

        onListPriceClose(ruleId, newPrices) {
            const listPriceCurrencies = newPrices.map((price) => this.getCurrency(price.currencyId));
            const productPrice = this.product.prices.find((productPriceCandidate) => productPriceCandidate.ruleId === ruleId);
            listPriceCurrencies.forEach((currency) => {
                if (this.isPriceFieldInherited(productPrice, currency)) {
                    this.onInheritanceRemove(productPrice, currency);
                }
            });

            const graduatedPrices = this.findPricesByRuleId(ruleId);

            graduatedPrices.forEach((graduatedPrice) => {
                graduatedPrice.price.forEach((price) => {
                    const newPrice = newPrices.find((priceCandidate) => priceCandidate.currencyId === price.currencyId);
                    if (newPrice && newPrice.listPrice) {
                        price.linked = newPrice.listPrice.linked;
                        price.listPrice = {
                            currencyId: newPrice.listPrice.currencyId,
                            gross: newPrice.listPrice.gross,
                            linked: newPrice.listPrice.linked,
                            net: newPrice.listPrice.net
                        };
                    } else {
                        delete price.listPrice;
                    }
                });
            });

            this.$set(this.displayListPrice, ruleId, false);
        }
    }
});
