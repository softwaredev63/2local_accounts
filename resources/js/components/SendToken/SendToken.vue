    <script>
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";
import "vue-form-wizard/dist/vue-form-wizard.min.css";
import { required } from "vuelidate/lib/validators";

export default {
  data() {
    return {
      isLastStep: false,
      isShowSuccessMessage: false,
      isShowFailedMessage: false,
      isApiErrorResponse: false,
      transactionHash: "",
      firstTab: {
        tokenSymbol: "2LC",
        toAddress: "",
        amount: "0",
        gasPrice: "0",
        gasLimit: "0",
      },
      secondTab: {
        secretPhrase: "",
      },
      thirdTab: {
        password: "",
      },
      backendValidation: {},
      labels: {
        tokenSymbol: "Token symbol",
        toAddress: "Address",
        amount: "Amount",
        gasPrice: "Gas price",
        gasLimit: "Gas limit",
        secretPhrase: "Secret phrase",
        password: "Password",
      },
    };
  },
  created: async function () {
    try {
      const response = await this.$http.get("/get-gas-price");
      const gasPrice = response.data / 10 ** 9;
      this.$v.firstTab.$model.gasPrice = gasPrice;      
    } catch (e) {
        console.error(e);
    }
  },
  computed: {
    maxAmount: function () {
      if (this.firstTab.tokenSymbol) {
        let token = this.tokens.find(
          (item) => item.symbol === this.firstTab.tokenSymbol
        );
        if (token && token.balance) {
          return token.balance;
        }
      }
      return 0;
    },
    transactionLink: function () {
      return `https://bscscan.com/tx/${this.transactionHash}`;
    },
  },
  comments: {
    FormWizard,
    TabContent,
    WizardStep,
  },
  props: {
    name: String,
    tokens: Array,
    isShowModal: Boolean,
    onClose: Function,
  },
  methods: {
    validateFirstTab: function () {
      this.$v.firstTab.$touch();
      return !(this.$v.firstTab.$pending || this.$v.firstTab.$error);
    },
    validateSecondTab: function () {
      this.$v.secondTab.$touch();
      return !(this.$v.secondTab.$pending || this.$v.secondTab.$error);
    },
    validateThirdTab: function () {
      return !(this.$v.thirdTab.$pending || this.$v.thirdTab.$error);
    },
    validationStatus: function (validation) {
      return typeof validation != "undefined" ? validation.$error : false;
    },
    onComplete: function () {
      this.handleSendToken();
    },
    handleOnClose: function () {
      this.$refs?.sendTokenWizardRef?.reset();
      this.onClose();
    },
    getCurrentWizardStep: function () {
      return this.$refs?.sendTokenWizardRef?.activeTabIndex || null;
    },
    getErrorMessageByModel: function (modelName, errorType) {
      switch (errorType) {
        case "required":
          return `The ${this.labels[modelName]} is required`;
        case "customError":
          if (this.backendValidation[modelName]) {
            return this.backendValidation[modelName][errorType];
          }
          return "";
        case "minValue":
          return `The ${this.labels[modelName]} has to be greater than 0`;
        default:
          return "Something went wrong!";
      }
    },
    getErrors: function (tab, modelName) {
      const validator = this.$v[tab][modelName];
      const errorKeys = Object.keys(validator.$params);
      let response = [];
      if (errorKeys) {
        response = errorKeys.map((key) => {
          return {
            show: !validator[key],
            message: this.getErrorMessageByModel(modelName, key),
          };
        });
      }
      return response;
    },
    getTransactionData: function () {
      let postData = {
        ...this.$v.firstTab.$model,
        ...this.$v.secondTab.$model,
        ...this.$v.thirdTab.$model,
      };
      postData.amount = postData.amount.toString();
      return postData;
    },
    showFailedTransaction: function () {
      this.isShowFailedMessage = true;
      this.isShowSuccessMessage = false;
    },
    showSuccessTransaction: function (txHash) {
      this.transactionHash = txHash;
      this.isShowFailedMessage = false;
      this.isShowSuccessMessage = true;
    },
    handleApiErrorResponse: function (errors) {
      this.isApiErrorResponse = true;
      this.backendValidation = errors;
      this.goToFirstTabOfWizardError();
    },
    handleSendToken: function () {
      const v = this;
      const postData = this.getTransactionData();
      this.$refs.sendTokenWizardRef.loading = true;
      v.$http
        .post("/send-crypto", postData)
        .then((response) => {
          const {
            data: { transactionHash },
          } = response;
          this.showSuccessTransaction(transactionHash);
        })
        .catch((error) => {
          const { response } = error;
          if (response) {
            const {
              data: { errors },
              status,
            } = response;
            if (status === 500) {
              this.showFailedTransaction();
              return;
            }
            if (errors) this.handleApiErrorResponse(errors);
          } else {
            this.showFailedTransaction();
          }
        })
        .finally(() => {
          this.$refs.sendTokenWizardRef.loading = false;
        });
    },
    handleInputChange: async function (e) {
      let keyName = e.target.id;
      let customError = this.backendValidation[keyName];
      if (customError) {
        delete this.backendValidation[keyName];
      }
      if (keyName === "amount") {
        let inputVal = e.target.value;
        if (inputVal >= this.maxAmount) {
          this.firstTab.amount = this.maxAmount;
        }
      }

      if (
        keyName === "toAddress" ||
        keyName === "tokenSymbol" ||
        keyName === "amount"
      ) {
        const postData = {
          ...this.$v.firstTab.$model,
        };

        try {
            const response = await this.$http.post("/get-gas-limit", postData); 
            this.$v.firstTab.$model.gasLimit = response.data;
        } catch(e) {
            console.error(e)
        }
      }
    },
    hasServerError: function (propertyName) {
      return !!this.backendValidation[propertyName];
    },
    goToFirstTabOfWizardError: function () {
      let errorKeys = Object.keys(this.backendValidation);
      if (errorKeys) {
        let tabIndex = 0;
        if (errorKeys.indexOf("password") > -1) {
          tabIndex = 2;
        } else if (errorKeys.indexOf("secretPhrase") > -1) {
          tabIndex = 1;
        }

        if (this.isApiErrorResponse) {
          this.isApiErrorResponse = false;
          this.$refs.sendTokenWizardRef.navigateToTab(tabIndex);
        }
      }
    },
  },
  validations() {
    const checkMinValue = (value) => {
      return value > 0;
    };

    return {
      firstTab: {
        tokenSymbol: {
          required,
          customError: function () {
            return !this.hasServerError("tokenSymbol");
          },
        },
        toAddress: {
          required,
          customError: function () {
            return !this.hasServerError("toAddress");
          },
        },
        amount: {
          required,
          minValue: checkMinValue,
          customError: function () {
            return !this.hasServerError("amount");
          },
        },
        gasPrice: {
          required,
          minValue: checkMinValue,
          customError: function () {
            return !this.hasServerError("gasPrice");
          },
        },
        gasLimit: {
          required,
          minValue: checkMinValue,
          customError: function () {
            return !this.hasServerError("gasLimit");
          },
        },
      },
      secondTab: {
        secretPhrase: {
          required,
          customError: function () {
            return !this.hasServerError("secretPhrase");
          },
        },
      },
      thirdTab: {
        password: {
          required,
          customError: function () {
            return !this.hasServerError("password");
          },
        },
      },
    };
  },
};
</script>

<template>
  <div
    v-if="isShowModal"
    name="Send Token"
    class="modal fade"
    v-bind:class="{ show: isShowModal }"
    id="sendTokenModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="sendTokenModalLabel"
    aria-modal="true"
  >
    <transition name="modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div
            class="backdrop"
            :class="{
              show:
                this.$refs.sendTokenWizardRef &&
                this.$refs.sendTokenWizardRef.loading,
            }"
          >
            <div class="lds-dual-ring"></div>
          </div>
          <div
            class="modal-header"
            :class="{ 'last-step': getCurrentWizardStep() === 4 }"
            v-if="!isShowFailedMessage && !isShowSuccessMessage"
          >
            <h5 class="modal-title" id="sendTokenModalLabel">Send</h5>
            <button
              type="button"
              class="close"
              v-on:click="handleOnClose"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form-wizard
              v-show="!isShowFailedMessage && !isShowSuccessMessage"
              title
              subtitle
              nextButtonText="Send"
              ref="sendTokenWizardRef"
              @on-complete="onComplete"
            >
              <tab-content title :before-change="validateFirstTab">
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label class="twolocal-input">
                      <span>Receiving Address</span>
                      <input
                        type="text"
                        id="toAddress"
                        name="toAddress"
                        for="toAddress"
                        v-model.trim="$v.firstTab.toAddress.$model"
                        @input="handleInputChange"
                        :class="{
                          'is-invalid': validationStatus($v.firstTab.toAddress),
                        }"
                      />
                      <div
                        v-for="error in getErrors('firstTab', 'toAddress')"
                        v-if="error.show"
                        class="invalid-feedback"
                      >
                        {{ error.message }}
                      </div>
                    </label>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="twolocal-input">
                      <span>Selector token</span>
                      <select
                        id="tokenSymbol"
                        name="tokenSymbol"
                        for="tokenSymbol"
                        v-model.trim="$v.firstTab.tokenSymbol.$model"
                        @change="handleInputChange"
                        :class="{
                          'is-invalid': validationStatus(
                            $v.firstTab.tokenSymbol
                          ),
                        }"
                      >
                        <option
                          v-for="(token, index) in tokens"
                          :value="token.symbol"
                        >
                          {{ token.symbol }}
                        </option>
                      </select>
                      <div
                        v-for="error in getErrors('firstTab', 'tokenSymbol')"
                        v-if="error.show"
                        class="invalid-feedback"
                      >
                        {{ error.message }}
                      </div>
                    </label>
                  </div>
                  <div class="col-md-6">
                    <label class="twolocal-input">
                      <span>Amount</span>
                      <span class="amount-suffix">{{
                        firstTab.tokenSymbol
                      }}</span>
                      <input
                        type="number"
                        v-model.trim="$v.firstTab.amount.$model"
                        @input="handleInputChange"
                        :max="maxAmount"
                        :min="0"
                        :class="{
                          'is-invalid': validationStatus($v.firstTab.amount),
                        }"
                        id="amount"
                        name="amount"
                        for="amount"
                      />
                      <div class="max-amount">Max: {{ maxAmount }}</div>
                      <div
                        v-for="error in getErrors('firstTab', 'amount')"
                        v-if="error.show"
                        class="invalid-feedback"
                      >
                        {{ error.message }}
                      </div>
                    </label>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="twolocal-input">
                      <span>Gas Price(GWEI)</span>
                      <input
                        type="number"
                        v-model.trim="$v.firstTab.gasPrice.$model"
                        @input="handleInputChange"
                        :class="{
                          'is-invalid': validationStatus($v.firstTab.gasPrice),
                        }"
                        id="gasPrice"
                        name="gasPrice"
                        for="gasPrice"
                      />
                      <div
                        v-for="error in getErrors('firstTab', 'gasPrice')"
                        v-if="error.show"
                        class="invalid-feedback"
                      >
                        {{ error.message }}
                      </div>
                    </label>
                  </div>
                  <div class="col-md-6">
                    <label class="twolocal-input">
                      <span>Gas Limit</span>
                      <input
                        type="number"
                        v-model.trim="$v.firstTab.gasLimit.$model"
                        @input="handleInputChange"
                        :class="{
                          'is-invalid': validationStatus($v.firstTab.gasLimit),
                        }"
                        id="gasLimit"
                        name="gasLimit"
                        for="gasLimit"
                      />
                      <div
                        v-for="error in getErrors('firstTab', 'gasLimit')"
                        v-if="error.show"
                        class="invalid-feedback"
                      >
                        {{ error.message }}
                      </div>
                    </label>
                  </div>
                </div>
              </tab-content>
              <tab-content
                name="Secret phrase"
                info="Secret phrase"
                :before-change="validateSecondTab"
              >
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label class="twolocal-input">
                      <span>Secret phrase</span>
                      <textarea
                        type="text"
                        v-model.trim="$v.secondTab.secretPhrase.$model"
                        @input="handleInputChange"
                        :class="{
                          'is-invalid': validationStatus(
                            $v.secondTab.secretPhrase
                          ),
                        }"
                        id="secretPhrase"
                        name="secretPhrase"
                        for="secretPhrase"
                      >
                      </textarea>
                      <div
                        v-for="error in getErrors('secondTab', 'secretPhrase')"
                        v-if="error.show"
                        class="invalid-feedback"
                      >
                        {{ error.message }}
                      </div>
                    </label>
                  </div>
                </div>
              </tab-content>
              <tab-content
                name="Password"
                info="Password"
                :before-change="validateThirdTab"
              >
                <div class="row">
                  <div class="col-md-12">
                    <label class="twolocal-input">
                      <span
                        >To confirm sending, please enter your wallet
                        password</span
                      >
                      <input
                        type="password"
                        v-model.trim="$v.thirdTab.password.$model"
                        @input="handleInputChange"
                        :class="{
                          'is-invalid': validationStatus($v.thirdTab.password),
                        }"
                        id="password"
                        name="password"
                        for="password"
                      />
                      <div
                        v-for="error in getErrors('thirdTab', 'password')"
                        v-if="error.show"
                        class="invalid-feedback"
                      >
                        {{ error.message }}
                      </div>
                    </label>
                  </div>
                </div>
              </tab-content>
            </form-wizard>
            <div class="success-message-container" v-if="isShowSuccessMessage">
              <div class="col-md-12">
                <button
                  type="button"
                  class="close"
                  v-on:click="handleOnClose"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
                <div class="icon-container">
                  <img src="/assets/check.svg" />
                </div>
                <h5 class="title">Success !</h5>
                <p>
                  You have sent
                  <strong
                    >{{ firstTab.amount }} {{ firstTab.tokenSymbol }}</strong
                  >
                  to
                </p>
                <p>{{ firstTab.toAddress }}</p>
                <p>
                  Transaction:
                  <a target="_blank" :href="transactionLink">{{
                    transactionHash
                  }}</a>
                </p>
                <div class="button-container">
                  <button class="btn btn-fill" v-on:click="handleOnClose">
                    Done
                  </button>
                </div>
              </div>
            </div>
            <div class="failed-message-container" v-if="isShowFailedMessage">
              <div class="col-md-12">
                <button
                  type="button"
                  class="close"
                  v-on:click="handleOnClose"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
                <div class="icon-container">
                  <img src="/assets/attention.svg" />
                </div>
                <h5 class="title">Failed !</h5>
                <p>
                  Please check your balance or the receiving adress and then
                  retry
                </p>
                <div class="button-container">
                  <button class="btn btn-fill" v-on:click="handleOnClose">
                    OK
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<style lang="scss">
#sendTokenModal {
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.5);

  .modal-dialog {
    display: flex;
    justify-content: center;
    min-width: 500px;

    .modal-content {
      top: unset;
      background: #ffffff 0% 0% no-repeat padding-box;
      box-shadow: 0px 5px 30px #ffffff4a;
      border-radius: 12px;

      .modal-body {
        padding: 20px 30px;
      }

      .modal-header {
        padding: 30;
      }
    }
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    flex-direction: row;

    .modal-title {
      text-align: left;
      font-size: 30px;
      font-weight: bold;
      letter-spacing: 0.09px;
      line-height: 36px;
      color: #1d2943;
    }

    button.close {
      width: 33px;
      height: 33px;
      background: #ebebeb 0% 0% no-repeat padding-box;
      border-radius: 8px;
      padding: 0;
      margin: 0;
    }
  }
}

.vue-form-wizard {
  padding-bottom: 0;

  .wizard-header {
    display: none;
  }

  .wizard-navigation {
    .wizard-tab-content {
      padding: 0;
    }

    .wizard-progress-with-circle {
      display: none;
    }

    .wizard-nav-pills {
      display: none;
    }
  }

  .wizard-btn {
    height: 45px;
    background: #df642b 0% 0% no-repeat padding-box !important;
    border-color: #df642b !important;
    border-radius: 12px;
    min-width: 200px;
  }

  .wizard-card-footer {
    padding: 10px 0 0 0;
  }

  .twolocal-input {
    span {
      text-align: left;
      font-size: 16px;
      font-weight: bold;
      letter-spacing: 0.5px;
      color: #1d2943;
      opacity: 0.6;
    }
    .max-amount {
      text-align: left;
      font-weight: bold;
      letter-spacing: 0.5px;
      color: #1d2943;
      opacity: 0.6;
      position: relative;
      font-size: 12px;
      bottom: -5px;
    }

    input,
    select,
    textarea {
      background: #ffffff 0% 0% no-repeat padding-box;
      border: 2px solid #e1e1e1 !important;
      border-radius: 10px;
      margin-top: 15px;
      height: 50px;
      font-size: 18px;
      font-weight: bold;
      letter-spacing: 0.06px;
      color: #1d2943;
    }

    textarea {
      height: 150px;
    }

    #amount {
      padding-right: 50px;
    }

    #password {
      margin-bottom: 20px;
    }

    .amount-suffix {
      position: absolute;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding-right: 15px;
      right: 0;
      top: 58px;

      &:before,
      &:after {
        content: unset;
      }
    }
  }
}

.success-message-container,
.failed-message-container {
  & > div {
    display: flex;
    justify-content: center;
    flex-direction: column;

    .close {
      position: absolute;
      right: 0;
      top: 0;
      background: #ebebeb 0% 0% no-repeat padding-box;
      border-radius: 8px;
      width: 30px;
      height: 30px;

      & > span {
        color: #a0a5ba;
      }
    }

    .title {
      text-align: center;
      font-weight: bold;
      font-size: 26px;
      line-height: 32px;
      color: #1d2943;
      padding: 20px 0 10px 0;
    }

    p {
      text-align: center;
      font-weight: bold;
      color: #777f8e;
      font-size: 16px;
      word-break: break-word;
      strong {
        color: #384666;
      }
    }

    .amount {
      font-weight: bold;
      color: #384666;
    }

    .icon-container {
      display: flex;
      justify-content: center;
      padding: 25px 0;

      img {
        width: 70px;
        height: 70px;
      }
    }
  }

  .button-container {
    margin-top: 30px;
    display: flex;
    justify-content: center;

    button {
      width: 200px;
      height: 60px;
      text-align: center;
      border-radius: 12px;
      justify-content: center;
    }
  }
}

.failed-message-container {
}

.backdrop {
  background: rgba(255, 255, 255, 0.5);
  bottom: 0;
  position: absolute;
  top: 0;
  width: 100%;
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9;
  border-radius: 12px;

  &.show {
    display: flex;
  }
}

.lds-dual-ring {
  display: inline-block;
  width: 80px;
  height: 80px;
}

.lds-dual-ring:after {
  content: " ";
  display: block;
  width: 64px;
  height: 64px;
  margin: 8px;
  border-radius: 50%;
  border: 6px solid #df642b;
  border-color: #df642b transparent #df642b transparent;
  animation: lds-dual-ring 1.2s linear infinite;
}

@keyframes lds-dual-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
