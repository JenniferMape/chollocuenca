import type { User } from './user.interface';

export interface AuthResponse {
  token: string;
  user: User;
  result: {
    user: User;
    token: string;
  };
  message?: string;
}
