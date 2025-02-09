import MaintanceModal from "../Components/MaintanceModal.vue";
import { Dialog } from "quasar";

export const commingSoonDialog = () => {
	Dialog.create({
		component: MaintanceModal,
	});
};