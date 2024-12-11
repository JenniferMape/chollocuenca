export interface Offer {
  id_offer: string;
  id: number;
  id_company_offer: number;
  id_category_offer: number;
  title_offer: string;
  new_price_offer: number;
  original_price_offer: number;
  description_offer: string;
  start_date_offer: string;
  end_date_offer: string;
  discount_code_offer: string | null;
  image_offer: string | null;
  web_offer: string | null;
  address_offer?: string | null;
  created_offer?: string;
  updated_offer?: string;
  category_name?: string;
  company_name?: string;
}
