// 📌 Definições globais de entidades do LibroVault

export type UserRole = "admin" | "standard" | "plus";

// Enum para os status de processamento do Book (baseado no campo `processing_status`)
export type ProcessingStatus = "pending" | "processing" | "completed";

// Tipagem do Usuário
export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole; // Enum
  created_at: string;
  updated_at: string;
}

// Tipagem do Livro
export interface Book {
  id: number;
  user: User;
  title: string;
  volume: integer;
  edition: string;
  pages: number;
  isbn: string;
  author: string;
  genre: string;
  publisher: string;
  description: string;
  year: number;
  thumbnail?: File;
  pdf?: File;
  processing_status: ProcessingStatus; // Enum
  created_at: string;
  updated_at: string;
}

// Tipagem da Página do Livro
export interface BookPage {
  id: number;
  book: Book;
  page_number: number;
  text: string;
  embedding_file: File;
  created_at: string;
  updated_at: string;
}

// Tipagem de Arquivo
export interface File {
  id: number;
  name: string;
  path: string;
  type: string;
  size: number;
  extension: string;
  expires_in?: string; // Para arquivos temporários
  created_at: string;
  updated_at: string;
  url: string;
}