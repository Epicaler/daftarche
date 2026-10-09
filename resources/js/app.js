import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import jdatepicker from './components/jdatepicker';
import moneyInput from './components/money-input';
import apexChart from './components/apex-chart';
import toasts from './components/toasts';

Alpine.data('jdatepicker', jdatepicker);
Alpine.data('moneyInput', moneyInput);
Alpine.data('apexChart', apexChart);
Alpine.data('toasts', toasts);

Livewire.start();
