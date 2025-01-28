import { AccordionCollection } from './Accordion';
import AddToFavoriteButtonCollection from './AddToFavoriteButton';
import DialogCollection from './Dialog';
import { FittingFormCollection } from './FittingForm';
import FormsValidator from './FormValidator';
import InputMaskCollection from './InputMask';
import ProductFilterFormCollection from './ProductFilterForm';
import ReviewFormCollection from './ReviewForm';
import './styles/index.scss';

new FormsValidator();

DialogCollection.init();
FittingFormCollection.init();
ReviewFormCollection.init();
InputMaskCollection.init();
AddToFavoriteButtonCollection.init();
ProductFilterFormCollection.init();
AccordionCollection.init();
