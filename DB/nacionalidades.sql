ALTER TABLE calles_recorridos 
DROP FOREIGN KEY calles_recorridos_FK;

ALTER TABLE calles_recorridos 
ADD CONSTRAINT calles_recorridos_FK 
FOREIGN KEY (id_recorrido) REFERENCES recorridos (id_recorrido) 
ON DELETE CASCADE;