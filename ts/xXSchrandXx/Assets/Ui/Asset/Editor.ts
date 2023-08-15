import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackObject, AjaxCallbackSetup } from "WoltLabSuite/Core/Ajax/Data";
import * as Core from "WoltLabSuite/Core/Core";
import { DialogCallbackObject, DialogCallbackSetup } from "WoltLabSuite/Core/Ui/Dialog/Data";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as Language from "WoltLabSuite/Core/Language";
import * as StringUtil from "WoltLabSuite/Core/StringUtil";
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";
import UiDialog from "WoltLabSuite/Core/Ui/Dialog";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class Editor implements AjaxCallbackObject, DialogCallbackObject {
  private actions = ["trash", "restore", "delete", "return"];
  private actionName = "";
  private readonly header: HTMLElement;

  constructor() {
    this.header = document.querySelector(".contentHeader") as HTMLElement;

    this.actions.forEach((action) => {
      const button = document.querySelector(".contentInteractionButtons .jsButtonAsset" + StringUtil.ucfirst(action)) as HTMLElement;

      // The button is missing if the current user lacks the permission.
      if (!button) {
        return;
      }
      button.dataset.action = action;
      button.addEventListener("click", (ev) => this._click(ev));
    });
  }

  _click(event: MouseEvent): void {
    event.preventDefault();

    const target = event.currentTarget as HTMLElement;
    this.actionName = target.dataset.action || "";

    this.actions.forEach((action) => {
      const button = document.querySelector(".contentInteractionButtons .jsButtonAsset" + StringUtil.ucfirst(action)) as HTMLElement;

      // The button is missing if the current user lacks the permission.
      if (!button) {
        return;
      }
      button.classList.add("disabled");
    });

    if (this.actionName == "delete") {
      UiConfirmation.show({
        confirm: () => {
          Ajax.api(this, {
            actionName: this.actionName
          });
        },
        cancel: () => {
          this.close();
        },
        message: Language.get("wcf.page.asset.button.delete.confirmMessage"),
        messageIsHtml: true
      });
    } else {
      UiDialog.open(this);
    }
  }

  _dialogSetup(): ReturnType<DialogCallbackSetup> {
    return {
      id: "wcfUiAssetEditor",
      options: {
        title: Language.get("wcf.page.asset.button." + this.actionName + ".confirmMessage"),
        onSetup: (content) => {
          const submitButton = content.querySelector("button.buttonPrimary") as HTMLButtonElement;
          submitButton.addEventListener("click", this._submit.bind(this));
        },
        onShow: (content) => {
          const reason = document.getElementById("wcfUiAssetEditorReason") as HTMLElement;
          let label = reason.nextElementSibling as HTMLElement;
          const phrase = "wcf.page.asset." + this.actionName + ".reason.description";
          label.textContent = Language.get(phrase);
          if (label.textContent === phrase) {
            DomUtil.hide(label);
          } else {
            DomUtil.show(label);
          }
        },
        onBeforeClose: (content) => {
          this.close();
          UiDialog.close("wcfUiAssetEditor");
        }
      },
      source: `<div class="section">
        <dl>
          <dt><label for="wcfUiAssetEditorReason">${Language.get("wcf.global.reason.optional")}</label></dt>
          <dd><textarea id="wcfUiAssetEditorReason" cols="40" rows="3"></textarea><small></small></dd>
        </dl>
      </div>
      <div class="formSubmit">
        <button type="button" class="button buttonPrimary">${Language.get("wcf.global.button.submit")}</button>
      </div>`,
    };
  }

  _submit(event: Event): void {
    event.preventDefault();

    const parameters = {};
    const reason = document.getElementById("wcfUiAssetEditorReason") as HTMLTextAreaElement;
    parameters["data"] = {
        "reason": reason.value.trim()
    };

    Ajax.api(this, {
      actionName: this.actionName,
      parameters: parameters,
    });

    UiDialog.close("wcfUiAssetEditor");
  }

  _ajaxSuccess(data): void {
    UiNotification.show();

    switch (data.actionName) {
      case "trash":
      case "restore": {
        window.location.reload();
        break;
      }

      case "delete":
        window.location.href = Language.get("wcf.page.asset.button.delete.redirect");
        break;
    }
  }

  _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
    return {
      data: {
        className: "assets\\data\\asset\\AssetAction",
        objectIDs: [+this.header.dataset.objectId!],
      },
    };
  }

  private close(): void {
    this.actions.forEach((action) => {
        const button = document.querySelector(".contentInteractionButtons .jsButtonAsset" + StringUtil.ucfirst(action)) as HTMLElement;

        // The button is missing if the current user lacks the permission.
        if (!button) {
          return;
        }
        button.classList.remove("disabled");
      });
  }
}

/**
 * Initializes the editor.
 */
export function init(): void {
  new Editor();
}