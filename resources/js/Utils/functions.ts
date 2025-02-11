import MaintanceModal from "../Components/MaintanceModal.vue";
import { Dialog } from "quasar";

export const commingSoonDialog = () => {
	Dialog.create({
		component: MaintanceModal,
	});
};

/**
 * Sleep function
 * @param milliseconds 
 */
export function sleep(milliseconds: number): void {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if (new Date().getTime() - start > milliseconds) {
            break;
        }
    }
}